<?php

use Database\Seeders\MoviesSeeder;

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('displays correct sort direction when already sorting by title asc', function() {
    get(route('movies.index', ['sort' => 'title']))
        ->assertOk()
        ->assertSeeInOrder([
            'Sort',
            'Title Z - A',
            'Release Date',
            'Purchase Date',
        ])
        ->assertSeeHtmlInOrder([
            'href="http://movie-inventory.test/movies?sort=title%7Cdesc"',
            'href="http://movie-inventory.test/movies?sort=release_date"',
            'href="http://movie-inventory.test/movies?sort=purchase_date"'
        ]);

})->todo('need to rewrite for updated component');

it('displays sort options above the movie list', function() {
    get(route('movies.index'))
        ->assertOk()
        ->assertSeeInOrder([
            'Sort',
            'Title A - Z',
            'Release Date',
            'Purchase Date'
        ])
        ->assertSeeHtmlInOrder([
            'href="http://movie-inventory.test/movies?sort=title"',
            'href="http://movie-inventory.test/movies?sort=release_date"',
            'href="http://movie-inventory.test/movies?sort=purchase_date"'
        ]);
})->todo('need to rewrite for updated component');
