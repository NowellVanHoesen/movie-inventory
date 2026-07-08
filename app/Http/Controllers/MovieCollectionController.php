<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieCollection;
use App\Traits\InteractsWithTMDB;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class MovieCollectionController extends Controller
{
    use InteractsWithTMDB;

    public function index()
    {
        $collections = MovieCollection::orderBy('name_sortable', 'asc')->paginate(24);

        $page_title = config('app.name') . ' - Movie Collections';

        return inertia('Movies/Collections/Index', [
            'collections' => Inertia::scroll($collections->toResourceCollection()),
            'page_title' => $page_title,
        ]);
    }

    public function show(MovieCollection $collection)
    {
        $collection_details = $this->getMovieCollection($collection->id);

        if (!$collection_details) {
            abort(404, 'Collection not found');
        }

        if (isset($collection_details->parts) && !is_array($collection_details->parts)) {
            $collection_details->parts = [];
        }

        $movie_ids = array_column($collection_details->parts, 'id');

        $movie_slugs = Movie::whereIn('id', $movie_ids)->pluck('slug','id');

        $collection_details->parts = collect($collection_details->parts)->map(function ($movie) use ( $movie_slugs ) {
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



        return view('movies.collections.show', compact('collection', 'collection_details', 'page_title'));
    }
}
