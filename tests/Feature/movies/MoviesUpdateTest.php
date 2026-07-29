<?php

use App\Models\MediaType;
use App\Models\Movie;
use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\patch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('requires authentication to update a movie', function () {
    $movie = Movie::where('imdb_id', 'tt1194173')->first();
    $originalPurchaseDate = $movie->purchase_date;

    patch(route('movies.update', $movie), [
        'purchase_date' => '2020-01-01',
        'media_type' => [],
    ])->assertRedirect(route('login'));

    expect($movie->refresh()->purchase_date)->toBe($originalPurchaseDate);
});

it('updates purchase date and media types for a movie', function () {
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();
    $dvd = MediaType::where('name', 'DVD')->firstOrFail();
    $blu_ray = MediaType::where('name', 'Blu-Ray')->firstOrFail();

    patch(route('movies.update', $movie), [
        'purchase_date' => '2020-06-15',
        'media_type' => [$dvd->id, $blu_ray->id],
    ])->assertRedirect(route('movies.show', $movie));

    $movie->refresh();

    expect($movie->purchase_date)->toBe('2020-06-15')
        ->and($movie->media_types->pluck('id')->sort()->values()->all())
        ->toBe(collect([$dvd->id, $blu_ray->id])->sort()->values()->all());
});

it('clears purchase date back to wishlist and detaches all media types when media_type is empty', function () {
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();
    expect($movie->media_types)->not->toBeEmpty();

    patch(route('movies.update', $movie), [
        'purchase_date' => null,
        'media_type' => [],
    ])->assertRedirect(route('movies.show', $movie));

    $movie->refresh();

    expect($movie->purchase_date)->toBeNull()
        ->and($movie->media_types)->toBeEmpty();
});

it('fails validation gracefully instead of crashing when media_type is omitted entirely', function () {
    // Regression test: previously `media_type => ['array']` didn't require the key to be
    // present, so an omitted key passed validation silently, and `array_flip($attributes['media_type'])`
    // then threw on the missing array key. The Vue form always sends `media_type` as an array
    // (even empty, see the test above), so this only matters for stray/API requests missing the
    // field entirely — it must now fail validation cleanly, not crash with a 500.
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    patch(route('movies.update', $movie), [
        'purchase_date' => $movie->purchase_date,
    ])->assertSessionHasErrors('media_type');
});

it('rejects an invalid purchase_date format', function () {
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    patch(route('movies.update', $movie), [
        'purchase_date' => 'not-a-date',
        'media_type' => [],
    ])->assertSessionHasErrors('purchase_date');
});

it('rejects a nonexistent media type id', function () {
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    patch(route('movies.update', $movie), [
        'purchase_date' => $movie->purchase_date,
        'media_type' => [999999],
    ])->assertSessionHasErrors('media_type.0');
});
