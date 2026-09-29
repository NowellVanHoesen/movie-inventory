<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMovieCastMembers;
use App\Jobs\ProcessMovieCollection;
use App\Models\Certification;
use App\Models\Genre;
use App\Models\Movie;
use App\Traits\AppliesIndexPreferences;
use App\Traits\InteractsWithTMDB;
use App\Traits\MediaTypeHelpers;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class MoviesController extends Controller {
    use AppliesIndexPreferences, InteractsWithTMDB, MediaTypeHelpers;

    /**
     * Display a listing of all wishlist and purchased movies.
     */
    public function index() {
        $query = Movie::with(['mediaTypes']);

        $pageTitleSuffix = 'Movie List';
        $defaultSortCol = 'release_date';

        if (Route::is('movies.purchased')) {
            $query->purchased();
            $pageTitleSuffix = 'Purchased Movies';
            $defaultSortCol = 'purchase_date';
        } elseif (Route::is('movies.wishlist')) {
            $query->wishlist();
            $pageTitleSuffix = 'Movie Wishlist';
        }

        $this->applyIndexPreferences(
            $query,
            ['genres' => 'selectedGenres', 'sortCol' => 'sortCol', 'sortDir' => 'sortDir'],
            titleCol: 'title_sortable',
            dateCol: 'release_date',
            defaultSortCol: $defaultSortCol,
            defaultSortDir: 'desc',
        );

        $movies = $query->paginate(24);

        $page_title = config('app.name') . ' - ' . $pageTitleSuffix;

        return inertia('Movies/Index', [
            'movies' => Inertia::scroll(fn () => $movies->toResourceCollection()),
            'page_title' => $page_title,
            // A closure so Inertia skips the query entirely on the `only: ['movies']`
            // partial reloads that infinite scroll and the filter panel both issue.
            'genres' => fn () => Genre::has('movies')->select('name')->orderBy('name')->get()->toResourceCollection(),
        ]);
    }

    /**
     * Display the form for creating a new movie.
     *
     * Accepts an HTTP request which may include prefill data (e.g. query parameters)
     * or contextual information. Prepares and returns the Inertia page used to render
     * the movie creation form (loading any required supporting data such as genres,
     * studios, etc.). May perform authorization checks and redirect if the user
     * is not permitted to create movies.
     *
     * @param  Request  $request  Incoming HTTP request with optional prefill/context data.
     * @return Response|RedirectResponse The Inertia response rendering the creation form, or a redirect on authorization/error conditions.
     *
     * @throws AuthorizationException If the user is not authorized to create a movie.
     */
    public function create(Request $request) {
        $data = [];

        if (! empty($request['query'])) {
            $attributes = $request->validate([
                'query' => ['min:2'],
                'year' => ['sometimes', 'max:4'],
            ]);

            // Escape LIKE wildcards, as SearchController does, so "100%" isn't match-all.
            $localResults = Movie::with('certification')
                ->where('title_normalized', 'like', '%' . addcslashes($attributes['query'], '%_\\') . '%')
                ->get();

            $data['local_results'] = $localResults->toResourceCollection();

            $data['search_results'] = $this->searchMovies(
                $attributes['query'],
                $attributes['year'] ?? null
            );

            $data['search_term'] = $attributes['query'];
            $data['search_year'] = $attributes['year'] ?? null;
        } elseif (! empty($request['movie_id'])) {
            $attributes = $request->validate([
                'movie_id' => ['integer'],
                'search_term' => ['min:2'],
                'search_year' => ['sometimes', 'max:4'],
            ]);

            $results = $this->getMovieDetail($attributes['movie_id']);

            if ($results === null) {
                return back()->withErrors(['movie_id' => 'Could not load that movie from TMDB. Please try again.']);
            }

            $genres = [];

            foreach ($results->genres as $genre) {
                $genres[] = $genre->name;
            }

            $results->genres = $genres;

            $data['media_types'] = $this->getMediaTypes();

            $data['movie'] = $results;

            $data['search_term'] = $attributes['search_term'] ?? '';
        }

        $data['page_title'] = config('app.name') . ' - Add Movie';

        return Inertia::render('Movies/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $attributes = $request->validate([
            'movie_id' => ['required', 'integer'],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'media_type' => ['array'],
            // Matches the rule update() already uses: without it, an unknown id
            // reaches attach() and fails on the pivot's foreign key instead.
            'media_type.*' => ['integer', 'exists:media_types,id'],
        ]);

        $movie_detail = $this->getMovieDetail($attributes['movie_id']);

        if ($movie_detail === null) {
            return back()->withErrors(['movie_id' => 'Could not load that movie from TMDB. Nothing was saved — please try again.']);
        }

        $certification_name = 'NR';
        $release_date = $movie_detail->release_date;

        foreach ($movie_detail->release_dates->results as $rDate) {
            if ($rDate->iso_3166_1 !== 'US') {
                continue;
            }

            foreach ($rDate->release_dates as $usReleaseDates) {
                if (! in_array($usReleaseDates->type, [3, 4], true) || empty($usReleaseDates->certification)) {
                    continue;
                }

                $release_date = Carbon::create($usReleaseDates->release_date)->toDateString();
                $certification_name = $usReleaseDates->certification;

                break 2;
            }
        }

        $movie = Movie::create([
            'id' => $movie_detail->id,
            'imdb_id' => $movie_detail->imdb_id ?: null,
            'title' => $movie_detail->title,
            'original_title' => $movie_detail->original_title,
            'tagline' => $movie_detail->tagline,
            'overview' => $movie_detail->overview,
            'release_date' => $release_date,
            'purchase_date' => $attributes['purchase_date'] ?? null,
            'poster_path' => $movie_detail->poster_path ?: null,
            'backdrop_path' => $movie_detail->backdrop_path ?: null,
            'certification_id' => Certification::idFor($certification_name),
            'runtime' => $movie_detail->runtime,
        ]);

        // syncWithoutDetaching over a per-row attach(): one insert instead of N, and
        // it won't trip the pivot's composite primary key if a row already exists.
        $movie->genres()->syncWithoutDetaching(
            collect($movie_detail->genres)->pluck('id')->all()
        );

        if (! empty($attributes['media_type'])) {
            $movie->mediaTypes()->syncWithoutDetaching($attributes['media_type']);
        }

        if (! is_null($movie_detail->belongs_to_collection)) {
            ProcessMovieCollection::dispatch($movie_detail->belongs_to_collection->id);
        }

        ProcessMovieCastMembers::dispatch($movie);

        return redirect()->route('movies.show', $movie);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie) {
        $movie->media_types_display = $this->getMediaTypesDisplay($movie->mediaTypes);

        $movie->load('collection', 'genres', 'castMembers', 'mediaTypes');

        return Inertia::modal('Movies/MovieModal', [
            'movie' => $movie,
            'media_type_options' => $this->getMediaTypes(),
        ], route('movies.index'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Movie $movie, Request $request) {
        $attributes = $request->validate([
            'movie_id' => ['integer'],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'media_type' => ['present', 'array'],
            'media_type.*' => ['integer', 'exists:media_types,id'],
        ]);

        if ($attributes['purchase_date'] !== $movie->purchase_date) {
            $movie->update(['purchase_date' => $attributes['purchase_date']]);
        }

        $movie->mediaTypes()->sync($attributes['media_type']);

        return redirect()->route('movies.show', $movie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie) {
        $movie->delete();

        // `message`, not `status`: Breeze flashes machine codes (e.g. `profile-updated`)
        // under `status`, and `message` is what the layout's flash banner displays.
        return Inertia::backFromModal()->with('message', "{$movie->title} deleted.");
    }
}
