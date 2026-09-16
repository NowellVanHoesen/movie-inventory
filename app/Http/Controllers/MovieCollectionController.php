<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieCollection;
use App\Traits\InteractsWithTMDB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class MovieCollectionController extends Controller {
    use InteractsWithTMDB;

    public function index() {
        $collections = MovieCollection::orderBy('name_sortable', 'asc')->paginate(24);

        $page_title = config('app.name') . ' - Movie Collections';

        return inertia('Movies/Collections/Index', [
            'collections' => Inertia::scroll($collections->toResourceCollection()),
            'page_title' => $page_title,
        ]);
    }

    public function show(MovieCollection $collection) {
        $cacheKey = "tmdb.collection.{$collection->id}";

        $collection_details = Cache::get($cacheKey);

        if ($collection_details === null) {
            $collection_details = $this->getMovieCollection($collection->id);

            if ($collection_details !== null) {
                Cache::put($cacheKey, $collection_details, now()->addDay());
            }
        }

        if (! $collection_details) {
            abort(404, 'Collection not found');
        }

        // TMDB can omit `parts` entirely, not just return it as a non-array, and
        // array_column() on a missing property is a TypeError rather than an empty list.
        if (! isset($collection_details->parts) || ! is_array($collection_details->parts)) {
            $collection_details->parts = [];
        }

        $movie_ids = array_column($collection_details->parts, 'id');

        $movie_slugs = Movie::whereIn('id', $movie_ids)->pluck('slug', 'id');

        $collection_details->parts = collect($collection_details->parts)->map(function ($movie) use ($movie_slugs) {
            return (object) [
                'id' => $movie->id,
                'slug' => $movie_slugs->get($movie->id, null),
                'title' => $movie->title,
                'release_date' => $movie->release_date,
                'poster_path' => $movie->poster_path,
                'backdrop_path' => $movie->backdrop_path,
            ];
        })->sortBy('release_date')->values()->all();

        $page_title = config('app.name') . ' - Collection: ' . $collection->name;

        return inertia('Movies/Collections/Show', [
            'page_title' => $page_title,
            'collection' => $collection,
            'collection_details' => $collection_details,
        ]);
    }
}
