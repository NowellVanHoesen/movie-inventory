<?php

use App\Models\Certification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('requires authentication to store a movie', function () {
    post(route('movies.store'), [
        'movie_id' => 27205,
        'purchase_date' => '2025-02-04',
        'media_type' => [],
    ])->assertRedirect(route('login'));

    $this->assertDatabaseMissing('movies', ['id' => 27205]);
});

it('falls back to NR when TMDB returns a certification we do not seed', function () {
    loginAsUser();
    Queue::fake();
    Http::preventStrayRequests();
    Http::fake([
        'api.themoviedb.org/3/movie/900001*' => Http::response([
            'id' => 900001,
            'imdb_id' => '',
            'title' => 'Unrated Cut',
            'original_title' => 'Unrated Cut',
            'tagline' => '',
            'overview' => '',
            'release_date' => '2020-01-01',
            'poster_path' => null,
            'backdrop_path' => null,
            'runtime' => 100,
            'genres' => [],
            'belongs_to_collection' => null,
            'release_dates' => ['results' => [[
                'iso_3166_1' => 'US',
                'release_dates' => [[
                    'type' => 4,
                    'certification' => 'Unrated',
                    'release_date' => '2020-02-01T00:00:00.000Z',
                ]],
            ]]],
        ]),
    ]);

    post(route('movies.store'), ['movie_id' => 900001, 'media_type' => []])
        ->assertRedirect();

    $this->assertDatabaseHas('movies', [
        'id' => 900001,
        'certification_id' => Certification::where('name', 'NR')->value('id'),
    ]);
});
