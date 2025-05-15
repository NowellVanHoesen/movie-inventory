<?php

use App\Http\Controllers\MovieCollectionController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

Route::controller(MoviesController::class)->group(function () {
    Route::get('/movies', 'index')->name('movies.index');
    Route::post('/movies', 'store')->name('movies.store');
    Route::get('/movies/wishlist', 'wishlist')->name('movies.wishlist');
    Route::get('/movies/purchased', 'purchased')->name('movies.purchased');
    Route::post('/movies/search', 'search')->name('movies.search');
    Route::get('/movies/create', 'create')->name('movies.create');
    Route::get('/movies/{movie:imdb_id}', 'show')->name('movies.show');
    Route::get('/movies/{movie:imdb_id}/edit', 'edit')->name('movies.edit');
    Route::patch('/movies/{movie:imdb_id}', 'update')->name('movies.update');
    Route::delete('/movies/{movie:imdb_id}', 'destroy')->name('movies.destroy');
});

Route::controller(MovieCollectionController::class)->group(function () {
    Route::get('/movieCollection', 'index')->name('movieCollection.index');
    Route::get('/movieCollection/{id}', 'show')->name('movieCollection.show');
});

Route::controller(SeriesController::class)->group(function () {
    Route::get('/series', 'index')->name('series.index');
    Route::post('/series', 'store')->name('series.store');
    Route::match(['get', 'post'], '/series/create', 'create')->name('series.create');
    Route::get('/series/{series}', 'show')->name('series.show');
    Route::get('/series/{series}/season/{season:season_number}', 'showSeason')->name('season.show');
    Route::get('/series/{series}/season/{season:season_number}/episode/{episode:episode_number}', 'showEpisode')->name('episode.show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
