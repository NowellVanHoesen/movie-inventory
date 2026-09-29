<?php

use App\Models\Movie;
use Database\Seeders\MoviesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\delete;
use function Pest\Laravel\from;
use function Pest\Laravel\withHeaders;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('requires authentication to delete a movie', function () {
    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    delete(route('movies.destroy', $movie))->assertRedirect(route('login'));

    expect(Movie::find($movie->id))->not->toBeNull();
});

it('deletes a movie and its pivot rows, redirecting to the page beneath the modal', function () {
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();
    $baseUrl = route('movies.wishlist');

    withHeaders(['X-Modal-Base-Url' => $baseUrl])
        ->delete(route('movies.destroy', $movie))
        ->assertRedirect($baseUrl)
        ->assertSessionHas('message', "{$movie->title} deleted.");

    expect(Movie::find($movie->id))->toBeNull();

    foreach (['genre_movie', 'media_type_movie', 'cast_member_movie'] as $pivot) {
        expect(DB::table($pivot)->where('movie_id', $movie->id)->exists())->toBeFalse();
    }
});

it('redirects back when no modal base url is sent', function () {
    loginAsUser();

    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    from(route('home'))
        ->delete(route('movies.destroy', $movie))
        ->assertRedirect(route('home'));

    expect(Movie::find($movie->id))->toBeNull();
});
