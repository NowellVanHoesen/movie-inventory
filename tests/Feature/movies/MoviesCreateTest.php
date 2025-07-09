<?php

use function Pest\Laravel\get;

it('has movies.create page', function () {
    loginAsUser();
    get(route('movies.create'))
        ->assertOk();
});

// movies.create
    // displays field to search TMDB API for movie
// movies.search
    // displays list of movied returned from TMDB API for search term
    // displays specific movie detail from TMDB API after clicking one of the returned movied
