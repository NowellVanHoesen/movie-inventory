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

it('displays both purchased and wishlist movies in order of release date desc then title asc', function () {
    $this->seed(MoviesSeeder::class);

    get(route('movies.index'))
        ->assertOk()
        ->assertSeeText([
            'The Bourne Identity',
            'The Bourne Supremacy',
            'The Bourne Ultimatum',
            'Bourne Legacy',
            'Jason Bourne',
            'Coming to America',
            'Coming 2 America'
        ])
        ->assertSeeInOrder([
            'Coming 2 America',
            'Jason Bourne',
            'Bourne Legacy',
            'The Bourne Ultimatum',
            'The Bourne Supremacy',
            'The Bourne Identity',
            'Coming to America',
        ]);
});

it('displays only wishlist movies in order of release date desc then title asc', function () {
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
            'Coming 2 America'
        ]);
});

it('displays only purchased movies in order of purchase date desc then release date desc', function () {
    $this->seed(MoviesSeeder::class);

    get(route('movies.purchased'))
        ->assertOk()
        ->assertDontSeeText([
            'Coming 2 America',
            'Gladiator II'
        ])
        ->assertSeeInOrder([
            'Coming to America',
            'The Bourne Ultimatum',
            'The Bourne Supremacy',
            'The Bourne Identity',
            'Jason Bourne',
            'Bourne Legacy',
        ]);
});

it('it does not display an add movie button when logged not in', function() {
    get(route('movies.index'))
        ->assertOk()
        ->assertDontSeeText('Add Movie')
        ->assertDontSee(route('movies.create'));
});

it('it displays an add movie button when logged in', function() {
    $user = loginAsUser();

    get(route('movies.index'))
        ->assertOk()
        ->assertSeeText('Add Movie')
        ->assertSee(route('movies.create'));
});
