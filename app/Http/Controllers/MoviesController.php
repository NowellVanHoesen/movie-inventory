<?php

namespace App\Http\Controllers;

use App\Jobs\processMovieCastMembers;
use App\Jobs\processMovieCollection;
use App\Models\Certification;
use App\Models\Genre;
use App\Models\Movie;
use App\Traits\InteractsWithTMDB;
use App\Traits\MediaTypeHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class MoviesController extends Controller
{
    use InteractsWithTMDB, MediaTypeHelpers;

    /**
     * Display a listing of all wishlist and purchased movies.
     */
    public function index()
    {
        $genres = Genre::has('movies')->select('name')->orderBy('name')->get();

        $query = Movie::with(['media_types']);

        $query->when(request()->header('X-Filter-Genres'), function ($query, $header) {
            $genreNames = is_array($header) ? $header : explode(',', $header);

            $query->whereHas('genres', function ($genreQuery) use ($genreNames) {
                $genreQuery->whereIn('name', $genreNames);
            });
        });

        $pageTitleSuffix = 'Movie List';

        $sortCol = request()->header('X-Sort-Col', 'release_date');
        $sortDir = request()->header('X-Sort-Dir', 'desc');
        $secondarySort = 'title_sortable';

        if (Route::is('movies.purchased')) {
            $query->purchased();
            $pageTitleSuffix = 'Purchased Movies';
            $sortCol = request()->header('X-Sort-Col', 'purchase_date');
        } elseif (Route::is('movies.wishlist')) {
            $query->wishlist();
            $pageTitleSuffix = 'Movie Wishlist';
        }

        if ( $sortCol === 'title_sortable' ) {
            $secondarySort = 'release_date';
        }

        if ( $sortDir === 'desc' ) {
            $query->orderByDesc( $sortCol )->orderBy( $secondarySort );
        } else {
            if ( $sortCol === 'purchase_date' ) {
                $query->orderByRaw('purchase_date is null');
            }

            $query->orderBy( $sortCol )->orderBy( $secondarySort );
        }

        $movies = $query->paginate(24);

        $page_title = config('app.name') . ' - ' . $pageTitleSuffix;

        return inertia('Movies/Index', [
            'movies' => Inertia::scroll(fn () => $movies->toResourceCollection()),
            'page_title' => $page_title,
            'genres' => $genres->toResourceCollection(),
        ]);
    }

    /**
     * Display the form for creating a new movie.
     *
     * Accepts an HTTP request which may include prefill data (e.g. query parameters)
     * or contextual information. Prepares and returns the view used to render the
     * movie creation form (loading any required supporting data such as genres,
     * studios, etc.). May perform authorization checks and redirect if the user
     * is not permitted to create movies.
     *
     * @param \Illuminate\Http\Request $request Incoming HTTP request with optional prefill/context data.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse The view rendering the creation form, or a redirect on authorization/error conditions.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to create a movie.
     */
    public function create(Request $request)
    {
        $data = [];

        if (! empty($request['query'])) {
            $attributes = $request->validate([
                'query' => ['min:2'],
                'year' => ['sometimes', 'max:4']
            ]);

            $data['local_results'] = Movie::where('title_normalized', 'like', '%' . $attributes['query'] . '%')->get();

            $data['search_results'] = $this->searchMovies(
                $attributes['query'],
                $attributes['year'] ? $attributes['year'] : null
            );

            $data['search_term'] = $attributes['query'];
            $data['search_year'] = $attributes['year'];
        } elseif (! empty($request['movie_id'])) {
            $attributes = $request->validate([
                'movie_id' => ['integer'],
                'search_term' => ['min:2'],
                'search_year' => ['sometimes', 'max:4'],
            ]);

            $results = $this->getMovieDetail($attributes['movie_id']);

            $genres = [];

            foreach ($results->genres as $genre) {
                $genres[] = $genre->name;
            }

            $results->genres = $genres;

            $data['media_types'] = $this->get_media_types();

            $data['movie'] = $results;

            $data['search_term'] = $attributes['search_term'] ?? '';
        }

        $data['page_title'] = config('app.name') . ' - Add Movie';

        return view('movies.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'movie_id' => ['integer'],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'media_type' => ['array'],
        ]);

        $movie_detail = $this->getMovieDetail($attributes['movie_id']);

        $certification_name = 'NR';
        $release_date = $movie_detail->release_date;

        foreach ($movie_detail->release_dates->results as $rDate) {
            if ($rDate->iso_3166_1 !== 'US') {
                continue;
            }

            foreach ($rDate->release_dates as $usReleaseDates) {
                if ( ! in_array( $usReleaseDates->type, [3,4], true ) || empty($usReleaseDates->certification)) {
                    continue;
                }

                $release_date = Carbon::create( $usReleaseDates->release_date )->toDateString();
                $certification_name = $usReleaseDates->certification;

                break 2;
            }
        }

        $certification = Certification::select('id')->where('name', '=', $certification_name)->first();

        $movie = Movie::create([
            'id' => $movie_detail->id,
            'imdb_id' => $movie_detail->imdb_id ?: null,
            'title' => $movie_detail->title,
            'original_title' => $movie_detail->original_title,
            'tagline' => $movie_detail->tagline,
            'overview' => $movie_detail->overview,
            'release_date' => $release_date,
            'purchase_date' => $attributes['purchase_date'],
            'poster_path' => $movie_detail->poster_path ?: null,
            'backdrop_path' => $movie_detail->backdrop_path ?: null,
            'certification_id' => $certification->id,
            'runtime' => $movie_detail->runtime,
        ]);

        foreach ($movie_detail->genres as $genre) {
            $movie->genres()->attach($genre->id);
        }

        if (! empty($attributes['media_type'])) {
            foreach ($attributes['media_type'] as $media_type_id) {
                $movie->media_types()->attach($media_type_id);
            }
        }

        if (! is_null($movie_detail->belongs_to_collection)) {
            processMovieCollection::dispatch($movie_detail->belongs_to_collection->id);
        }

        processMovieCastMembers::dispatch($movie);

        return redirect()->route('movies.show', $movie);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        $movie->media_types_display = $this->get_media_types_display($movie->media_types);

        // $recommendations = $this->getMovieRecommendations( $movie->id );

        // $owned_recommendations = Movie::whereIn( 'id', Arr::pluck($recommendations, 'id') )->get();

        // $page_title = config('app.name') . ' - Movie: ' . $movie->title;

        $movie->load('collection', 'genres', 'cast_members');

        return Inertia::modal('Movies/MovieModal', [
            'movie' => $movie,
            // 'recommendations' => $recommendations,
            // 'owned_recommendations' => $owned_recommendations,
            // 'page_title' => $page_title,
        ], route('movies.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        $media_types = $this->get_media_types();

        $page_title = config('app.name') . ' - Edit Movie: ' . $movie->title;

        return view('movies.edit', compact('movie', 'media_types', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Movie $movie, Request $request)
    {
        $attributes = $request->validate([
            'movie_id' => ['integer'],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'media_type' => ['array'],
        ]);

        if ($attributes['purchase_date'] !== $movie->purchase_date) {
            $movie->update(['purchase_date' => $attributes['purchase_date']]);
        }

        // attach media types that were added
        if (! empty($attributes['media_type'])) {
            foreach ($attributes['media_type'] as $media_type_id) {
                if (! $movie->media_types->contains($media_type_id)) {
                    $movie->media_types()->attach($media_type_id);
                }
            }
        }

        $media_types_flipped = array_flip($attributes['media_type']);

        // detach media types that were removed
        foreach ($movie->media_types as $assigned_type) {
            if (! array_key_exists($assigned_type->id, $media_types_flipped)) {
                $movie->media_types()->detach($assigned_type->id);
            }
        }

        return redirect()->route('movies.show', $movie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();

        return redirect()->route('movies.index')->with('status', 'Movie deleted successfully.');
    }
}
