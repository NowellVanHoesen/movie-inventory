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
        $movies = Movie::orderByDesc('purchase_date')->limit(14)->get();
        $series = Series::orderByDesc('purchase_date')->limit(7)->get();

        return view('home', [
            'movies' => $movies,
            'series' => $series,
        ]);
    }
}
