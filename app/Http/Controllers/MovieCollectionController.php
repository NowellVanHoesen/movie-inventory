<?php

namespace App\Http\Controllers;

use App\Models\MovieCollection;
use App\Traits\InteractsWithTMDB;
use Illuminate\Support\Arr;

class MovieCollectionController extends Controller
{
    use InteractsWithTMDB;

    public function index()
    {
        $collections = MovieCollection::orderBy('name', 'asc')->simplePaginate(14);

        $page_title = config('app.name') . ' - Movie Collections';

        return view('movies.collections.index', compact('collections', 'page_title'));
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

        $collection_details->parts = collect($collection_details->parts)->map(function ($movie) {
            return (object) [
                'id' => $movie->id,
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
