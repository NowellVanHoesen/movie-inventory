<?php

namespace App\Http\Controllers;

use App\Jobs\processMovieCastMembers;
use App\Jobs\processMovieCollection;
use App\Models\Certification;
use App\Models\MediaType;
use App\Models\Movie;
use App\Traits\InteractsWithTMDB;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    use InteractsWithTMDB;

    /**
     * Display a listing of all wishlist and purchased movies.
     */
    public function index()
    {
        return view('movies.index', [
            'movies' => Movie::orderByDesc('release_date')->simplePaginate(24),
        ]);
    }

    /**
     * Display a listing of wishlist movies.
     */
    public function wishlist()
    {
        return view('movies.index', [
            'movies' => Movie::wishlist()->orderByDesc('release_date')->simplePaginate(24),
        ]);
    }

    /**
     * Display a listing of purchased movies.
     */
    public function purchased()
    {
        return view('movies.index', [
            'movies' => Movie::purchased()->orderByDesc('purchase_date')->simplePaginate(24),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('movies.create');
    }

    public function search(Request $request)
    {
        if (! empty($request['query'])) {
            $attributes = $request->validate([
                'query' => ['min:2'],
            ]);

            $data['local_results'] = Movie::search($attributes['query'])->get();

            $data['search_results'] = $this->searchMovies($attributes['query']);
        } elseif (! empty($request['movie_id'])) {
            $attributes = $request->validate([
                'movie_id' => ['integer'],
            ]);

            $results = $this->getMovieDetail($attributes['movie_id']);

            $genres = [];

            foreach ($results->genres as $genre) {
                $genres[] = $genre->name;
            }

            $results->genres = $genres;

            $data['media_types'] = $this->get_media_types();

            $data['movie'] = $results;
        } else {
            return redirect(route('movies.create'));
        }

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

        foreach ($movie_detail->release_dates->results as $rDate) {
            if ($rDate->iso_3166_1 !== 'US') {
                continue;
            }

            foreach ($rDate->release_dates as $usReleaseDates) {
                if ($usReleaseDates->type < 3 || empty($usReleaseDates->certification)) {
                    continue;
                }

                $certification_name = $usReleaseDates->certification;
            }
        }

        $certification = Certification::select('id')->where('name', '=', $certification_name)->first();

        $movie = Movie::create([
            'id' => $movie_detail->id,
            'imdb_id' => $movie_detail->imdb_id,
            'title' => $movie_detail->title,
            'original_title' => $movie_detail->original_title,
            'tagline' => $movie_detail->tagline,
            'overview' => $movie_detail->overview,
            'release_date' => $movie_detail->release_date,
            'purchase_date' => $attributes['purchase_date'],
            'poster_path' => $movie_detail->poster_path,
            'backdrop_path' => $movie_detail->backdrop_path ?? '',
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

        return redirect('/movies/'.$movie_detail->imdb_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        $movie->media_types_display = $this->get_media_types_display($movie->media_types);

        return view('movies.show', ['movie' => $movie]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        return view('movies.edit', ['movie' => $movie, 'media_types' => $this->get_media_types()]);
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

        return redirect('/movies/'.$movie->imdb_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }

    private function get_media_types()
    {
        $media_types = MediaType::orderBy('parent_id')->orderBy('name')->get();

        $media_types_arr = [];

        foreach ($media_types as $type) {
            if ($type->parent_id === 0) {
                $media_types_arr[$type->name] = $media_types->filter(function ($item) use ($type) {
                    return $type->id === $item->parent_id;
                })->pluck('name', 'id');
            }
        }

        return $media_types_arr;

    }

    private function get_media_types_display($media_types)
    {
        $media_types_display = [];

        if (empty($media_types)) {
            return $media_types_display;
        }

        foreach ($media_types as $type) {
            $tmp_media_type_arr = [$type->name];

            $current_parent_id = $type->parent_id;

            while ($current_parent_id !== 0) {
                $media_type_parent = MediaType::find($current_parent_id);
                $tmp_media_type_arr = [$media_type_parent->name => $tmp_media_type_arr];
                $current_parent_id = $media_type_parent->parent_id;
            }

            $media_types_display = array_merge_recursive($media_types_display, $tmp_media_type_arr);
        }

        return $media_types_display;
    }
}
