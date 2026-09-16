<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieCollectionResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\SeriesResource;
use App\Models\Movie;
use App\Models\MovieCollection;
use App\Models\Series;
use Illuminate\Http\Request;

class SearchController extends Controller {
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request) {
        $movies_results = collect();
        $collections_results = collect();
        $series_results = collect();

        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->search) {
            // Escape LIKE wildcards so a search for "100%" doesn't turn into a
            // match-everything query.
            $term = '%' . addcslashes($request->search, '%_\\') . '%';

            $movies_results = Movie::where('title_normalized', 'like', $term)->orderByDesc('purchase_date')->limit(24)->get();
            $collections_results = MovieCollection::where('name_normalized', 'like', $term)->orderBy('name')->limit(24)->get();
            $series_results = Series::where('name_normalized', 'like', $term)->orderByDesc('first_air_date')->limit(24)->get();
        }

        $page_title = config('app.name') . ' - Search Results for: ' . $request->search;

        return inertia('Search/Index', [
            'movies' => MovieResource::collection($movies_results),
            'collections' => MovieCollectionResource::collection($collections_results),
            'series' => SeriesResource::collection($series_results),
            'search' => $request->search,
            'page_title' => $page_title,
        ]);
    }
}
