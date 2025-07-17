<?php

namespace App\Http\Controllers;

use App\Jobs\processMovieCastMembers;
use App\Jobs\processMovieCollection;
use App\Models\Certification;
use App\Models\Movie;
use App\Traits\InteractsWithTMDB;
use App\Traits\MediaTypeHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MoviesController extends Controller
{
    use InteractsWithTMDB, MediaTypeHelpers;

    /**
     * Display a listing of all wishlist and purchased movies.
     */
    public function index()
    {
        $query = Movie::select('title', 'slug', 'release_date', 'purchase_date', 'poster_path');

        $pageTitleSuffix = 'Movie List';

        $sort = request()->input('sort', 'release_date|desc');

        if (request()->has('purchased')) {
            $query->purchased();
            $pageTitleSuffix = 'Purchased Movies';
            $sort = request()->input('sort', 'purchase_date|desc');
        } elseif (request()->has('wishlist')) {
            $query->wishlist();
            $pageTitleSuffix = 'Movie Wishlist';
        }

        switch ($sort) {
            case 'purchase_date|desc':
                $query->orderByDesc('purchase_date')->orderBy('title_sortable');
                break;
            case 'purchase_date':
                $query->orderByRaw('purchase_date is null')->orderBy('purchase_date')->orderBy('title_sortable');
                break;
            case 'title|desc':
                $query->orderByDesc('title_sortable')->orderBy('release_date');
                break;
            case 'title':
                $query->orderBy('title_sortable')->orderBy('release_date');
                break;
            case 'release_date|desc':
                $query->orderByDesc('release_date')->orderBy('title_sortable');
                break;
            case 'release_date':
            default:
                $query->orderBy('release_date')->orderBy('title_sortable');
        }

        $movies = $query->simplePaginate(14);

        $page_title = config('app.name') . ' - ' . $pageTitleSuffix;

        return view('movies.index', compact('page_title', 'movies'));
    }

    /**
     * Show the form for adding a movie.
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

        $recommendations = $this->getMovieRecommendations( $movie->id );

        $owned_recommendations = Movie::whereIn( 'id', Arr::pluck($recommendations, 'id') )->get();

        $page_title = config('app.name') . ' - Movie: ' . $movie->title;

        return view('movies.show', compact('movie', 'recommendations', 'owned_recommendations', 'page_title'));
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
