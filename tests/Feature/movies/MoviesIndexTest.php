<?php

use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has movies index page', function () {
    $this->seed(MoviesSeeder::class);

    get(route('movies.index'))
    ->assertOk();
});

it('does not display an add movie button when logged not in', function() {
    get(route('movies.index'))
        ->assertOk()
        ->assertDontSeeText('Add Movie')
        ->assertDontSee(route('movies.create'));
});

it('displays an add movie button when logged in', function() {
    loginAsUser();

    get(route('movies.index'))
        ->assertOk()
        ->assertSeeText('Add Movie')
        ->assertSee(route('movies.create'));
});

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
});

it('displays both purchased and wishlist movies in default order (release date desc then title asc)', function () {
    $this->seed(MoviesSeeder::class);

    get(route('movies.index'))
        ->assertOk()
        ->assertSee([
            'Wild Robot',
            'Ambush',
            'My Days of Mercy',
            'Gladiator II',
            'The Forever Purge',
            'American Pie',
            'Dirty Angels'
        ])
        ->assertSeeInOrder([
            'Into the Deep',
            'Dirty Angels',
            'Gladiator II',
            'Wicked',
            'The Beekeeper',
            'Jurassic World Dominion',
            'The First Purge',
        ]);
});

it('displays only wishlist movies in default order (release date desc then title asc)', function () {
    $this->seed(MoviesSeeder::class);

    get(route('movies.wishlist'))
        ->assertOk()
        ->assertDontSeeText([
            'The Bourne Identity',
            'The Bourne Supremacy',
            'The Bourne Ultimatum',
            'Bourne Legacy',
            'Jason Bourne',
        ])
        ->assertSeeInOrder([
            'Gladiator II',
            'Wicked',
            'After We Fell'
        ]);
});

it('displays only purchased movies in default order (purchase date desc then release date desc)', function () {
    $this->seed(MoviesSeeder::class);

    get(route('movies.purchased'))
        ->assertOk()
        ->assertDontSeeText([
            'After We Fell',
            'Gladiator II',
            'Wicked'
        ])
        ->assertSeeInOrder([
            'Into the Deep',
            'Step Up 3',
            'Totally Baked',
            'Bolt',
            'After Ever Happy',
        ]);
});

it('displays correct sort direction when already sorting by title asc', function() {
    $this->seed(MoviesSeeder::class);

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

});
