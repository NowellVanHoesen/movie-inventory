<?php

use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has movies.create page', function () {
    loginAsUser();
    get(route('movies.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Movies/Create')
            ->etc()
        );
});

it('shows search results for a query, flagging movies already in the local library', function () {
    loginAsUser();
    $this->seed(MoviesSeeder::class);

    get(route('movies.create', ['query' => 'Gladiator']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Movies/Create')
            ->where('search_term', 'Gladiator')
            ->has('search_results')
            ->has('local_results')
            ->etc()
        );
});

it('shows TMDB movie detail when a movie_id is provided', function () {
    loginAsUser();

    // TMDB id 98 is "Gladiator" — a stable, real movie id used for this lookup.
    get(route('movies.create', ['movie_id' => 98, 'search_term' => 'Gladiator']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Movies/Create')
            ->where('movie.id', 98)
            ->has('media_types')
            ->etc()
        );
});
