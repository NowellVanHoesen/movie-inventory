<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $movies = Movie::with(['genres','cast_members','collection'])
            ->orderByDesc('purchase_date')
            ->limit(12)
            ->get();
        $series = Series::with(['genres','cast_members'])
            ->orderByDesc('purchase_date')
            ->limit(6)
            ->get();

        return inertia('Home', [
            'movies' => $movies->toResourceCollection(),
            'series' => $series->toResourceCollection(),
        ]);
    }
}
