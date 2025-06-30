<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieCollectionController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::get('/search', SearchController::class)->name('search');

Route::controller(MoviesController::class)->group(function () {
    Route::get('/movies', 'index')->name('movies.index');
    Route::post('/movies', 'store')->name('movies.store');
    Route::get('/movies/wishlist', 'wishlist')->name('movies.wishlist');
    Route::get('/movies/purchased', 'purchased')->name('movies.purchased');
    Route::match(['get','post'],'/movies/create', 'create')->middleware(['auth'])->name('movies.create');
    Route::get('/movies/{movie}', 'show')->name('movies.show');
    Route::get('/movies/{movie}/edit', 'edit')->middleware(['auth'])->name('movies.edit');
    Route::patch('/movies/{movie}', 'update')->middleware(['auth'])->name('movies.update');
    Route::delete('/movies/{movie}', 'destroy')->middleware(['auth'])->name('movies.destroy');
});

Route::controller(MovieCollectionController::class)->group(function () {
    Route::get('/movieCollection', 'index')->name('movieCollection.index');
    Route::get('/movieCollection/{collection}', 'show')->name('movieCollection.show');
});

Route::controller(SeriesController::class)->group(function () {
    Route::get('/series', 'index')->name('series.index');
    Route::post('/series', 'store')->name('series.store');
    Route::match(['get', 'post'], '/series/create', 'create')->middleware(['auth'])->name('series.create');
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
