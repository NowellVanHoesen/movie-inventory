<?php

namespace App\Http\Controllers;

use App\Models\MovieCollection;
use App\Traits\InteractsWithTMDB;
use Illuminate\Http\Request;

class MovieCollectionController extends Controller
{
    use InteractsWithTMDB;

    public function index() {
        return view('movies.collections.index', [ 'collections' => MovieCollection::orderBy('name', 'asc')->simplePaginate(24) ]);
    }

    public function show( int $id ) {
        $collection = MovieCollection::where('id', $id)->first();
        $collection_details = $this->getMovieCollection( $id );

        return view('movies.collections.show', [ 'collection' => $collection, 'collection_details' => $collection_details ]);
    }
}
