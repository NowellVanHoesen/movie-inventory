<?php

use App\Models\Movie;
use Tests\Browser;

it('can open a modal and close it on home route', function () {
    $this->browse(function (Browser $browser) {
        $movie = Movie::where( 'slug', 'step-up-3d' )->firstOrFail();

        $browser
            ->loginAs( 1 )
            ->visit( route('home') )
            ->waitFor( 'footer' )
            ->press( '@movie-btn-step-up-3d' )
            ->waitForText( 'Cast Members' )
            ->assertUrlIs( route( 'movies.show', $movie ) )
            ->clickAtPoint( 25, 25 )
            ->waitUntilMissingModal()
            ->pause(100)
            ->assertRouteIs( 'home' );
    });
});

it('can open a modal and close it on movies.index route', function () {
    $this->browse(function (Browser $browser) {
        $movie = Movie::orderByDesc('release_date')
            ->orderBy('title_sortable')
            ->first();

        $browser
            ->loginAs( 1 )
            ->visit( route('movies.index') )
            ->waitFor( "@movie-btn-{$movie->slug}" )
            ->press( "@movie-btn-{$movie->slug}" )
            ->waitForText( 'Cast Members' )
            ->assertUrlIs( route( 'movies.show', $movie ) )
            ->clickAtPoint( 25, 25 )
            ->waitUntilMissingModal()
            ->pause(100)
            ->assertUrlIs( route('movies.index') );
    });
});
