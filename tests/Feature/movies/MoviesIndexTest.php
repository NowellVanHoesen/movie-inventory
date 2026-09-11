<?php

use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('has movies index page', function () {
    get(route('movies.index'))
    ->assertOk();
});

it('displays both purchased and wishlist movies in default order (release date desc then title asc)', function () {
    get(route('movies.index'))
        ->assertOk()
        ->assertSee([
            'Wild Robot',
            'Ambush',
            'Into the Deep',
            'Gladiator II',
            'Last Seen Alive',
            'Dirty Angels'
        ])
        ->assertSeeInOrder([
            'Into the Deep',
            'Dirty Angels',
            'Gladiator II',
            'Wicked',
            'Knox Goes Away',
            'The Beekeeper',
            'Jurassic World Dominion',
        ]);
});

it('displays only wishlist movies in default order (release date desc then title asc)', function () {
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

it('sorts by the sortCol/sortDir cookies, so preferences survive a hard refresh', function () {
    $this->withUnencryptedCookies(['sortCol' => 'title_sortable', 'sortDir' => 'asc'])
        ->get(route('movies.index'))
        ->assertOk()
        ->assertSeeInOrder([
            'After',
            'After Ever Happy',
            'After We Fell',
        ]);
});

it('displays only purchased movies in default order (purchase date desc then release date desc)', function () {
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
            'My Days of Mercy',
            'Twin Peaks: Fire Walk with Me',
        ]);
});
