<?php

use App\Http\Controllers\CastMemberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieCollectionController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/search', SearchController::class)->name('search');

Route::controller(MoviesController::class)->group(function () {
    Route::get('/movies', 'index')->name('movies.index');
    Route::post('/movies', 'store')->name('movies.store');
    Route::match(['get','post'],'/movies/create', 'create')->middleware(['auth'])->name('movies.create');
    Route::get('/movies/purchased', 'index')->name('movies.purchased');
    Route::get('/movies/wishlist', 'index')->name('movies.wishlist');
    Route::get('/movies/{movie}', 'show')->name('movies.show');
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
});

Route::get('/cast/{castMember}', CastMemberController::class)->name('castMember');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
