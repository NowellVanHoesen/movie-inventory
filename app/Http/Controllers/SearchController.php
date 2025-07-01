<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieCollection;
use App\Models\Series;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $movies_results = collect();
        $collections_results = collect();
        $series_results = collect();

        if ($request->search) {
            $movies_results = Movie::where('title_normalized', 'like', '%' . $request->search . '%')->orderByDesc('purchase_date')->limit(14)->get();
            $collections_results = MovieCollection::where('name_normalized', 'like', '%' . $request->search . '%')->orderBy('name')->limit(14)->get();
            $series_results = Series::where('name_normalized', 'like', '%' . $request->search . '%')->orderByDesc('first_air_date')->limit(14)->get();
        }

        return view('search', [
            'movies_results' => $movies_results,
            'collections_results' => $collections_results,
            'series_results' => $series_results,
        ]);
    }
}
