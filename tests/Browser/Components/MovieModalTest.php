<?php

use App\Models\MediaType;
use App\Models\Movie;
use App\Models\User;
use Database\Seeders\MoviesSeeder;
use Tests\Browser;

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

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

it('does not shift the background page layout when opening or closing a modal', function () {
    $this->browse(function (Browser $browser) {
        $movie = Movie::orderByDesc('release_date')
            ->orderBy('title_sortable')
            ->first();

        $browser
            ->loginAs(1)
            ->visit(route('movies.index'))
            ->waitFor("@movie-btn-{$movie->slug}");

        $rectBefore = $browser->script("return JSON.stringify(document.querySelector('[dusk^=movie-btn-]').getBoundingClientRect());")[0];

        $browser
            ->press("@movie-btn-{$movie->slug}")
            ->waitForText('Cast Members');

        $rectDuring = $browser->script("return JSON.stringify(document.querySelector('[dusk^=movie-btn-]').getBoundingClientRect());")[0];

        $browser
            ->clickAtPoint(25, 25)
            ->waitUntilMissingModal()
            ->pause(100);

        $rectAfter = $browser->script("return JSON.stringify(document.querySelector('[dusk^=movie-btn-]').getBoundingClientRect());")[0];

        expect($rectDuring)->toBe($rectBefore);
        expect($rectAfter)->toBe($rectBefore);
    });
});

it('does not reset the movie list scroll position when opening or closing a modal after infinite scroll has loaded more pages', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(1)
            ->visit(route('movies.index'))
            ->waitFor('[dusk^=movie-btn-]');

        // Scroll to the bottom repeatedly to give the InfiniteScroll component
        // a chance to load additional pages beyond the initial 24 movies.
        foreach (range(1, 6) as $_) {
            $browser->script('window.scrollTo(0, document.body.scrollHeight);');
            $browser->pause(300);
        }

        $posterCountBefore = $browser->script("return document.querySelectorAll('[dusk^=movie-btn-]').length;")[0];
        expect($posterCountBefore)->toBeGreaterThan(24);

        $lastDuskId = $browser->script("
            const buttons = document.querySelectorAll('[dusk^=movie-btn-]');
            return buttons[buttons.length - 1].getAttribute('dusk');
        ")[0];

        $scrollYBeforeOpen = $browser->script('return window.scrollY;')[0];
        expect($scrollYBeforeOpen)->toBeGreaterThan(0);

        $browser
            ->click("[dusk=\"{$lastDuskId}\"]")
            ->waitForText('Cast Members');

        $scrollYWhileOpen = $browser->script('return window.scrollY;')[0];
        expect($scrollYWhileOpen)->toBeGreaterThan($scrollYBeforeOpen - 50);

        $browser
            ->clickAtPoint(25, 25)
            ->waitUntilMissingModal()
            ->pause(300);

        $scrollYAfterClose = $browser->script('return window.scrollY;')[0];
        $posterCountAfterClose = $browser->script("return document.querySelectorAll('[dusk^=movie-btn-]').length;")[0];

        expect($scrollYAfterClose)->toBeGreaterThan($scrollYBeforeOpen - 50);
        expect($posterCountAfterClose)->toBeGreaterThanOrEqual($posterCountBefore);
    });
});

it('toggles the modal into an inline edit form and cancel discards unsaved changes', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();
        $movie = Movie::whereNotNull('purchase_date')->whereHas('media_types')->firstOrFail();
        $mediaTypeId = $movie->media_types->first()->id;

        $browser
            ->loginAs($user)
            ->visit(route('movies.show', $movie))
            ->waitForText('Cast Members')
            ->assertUrlIs(route('movies.show', $movie))
            ->press('@edit-movie-btn')
            ->waitFor('@save-movie-btn')
            ->assertChecked("@media-type-{$mediaTypeId}")
            ->uncheck("@media-type-{$mediaTypeId}")
            ->assertNotChecked("@media-type-{$mediaTypeId}")
            ->press('@cancel-edit-btn')
            ->waitForText('Cast Members')
            ->assertUrlIs(route('movies.show', $movie))
            ->press('@edit-movie-btn')
            ->waitFor('@save-movie-btn')
            ->assertChecked("@media-type-{$mediaTypeId}");
    });
});

it('saves media type changes and returns to the read-only view', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();
        $movie = Movie::whereNotNull('purchase_date')->whereHas('media_types')->firstOrFail();
        $newMediaType = MediaType::where('parent_id', '!=', 0)
            ->whereNotIn('id', $movie->media_types->pluck('id'))
            ->firstOrFail();

        $browser
            ->loginAs($user)
            ->visit(route('movies.show', $movie))
            ->waitForText('Cast Members')
            ->press('@edit-movie-btn')
            ->waitFor('@save-movie-btn')
            ->check("@media-type-{$newMediaType->id}")
            ->assertChecked("@media-type-{$newMediaType->id}")
            ->press('@save-movie-btn')
            ->waitForText('Cast Members')
            ->assertUrlIs(route('movies.show', $movie));

        expect($movie->refresh()->media_types->pluck('id'))->toContain($newMediaType->id);
    });
});

it('can uncheck every media type and save without error', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();
        $movie = Movie::whereNotNull('purchase_date')->whereHas('media_types')->firstOrFail();
        $mediaTypeIds = $movie->media_types->pluck('id')->all();

        $browser
            ->loginAs($user)
            ->visit(route('movies.show', $movie))
            ->waitForText('Cast Members')
            ->press('@edit-movie-btn')
            ->waitFor('@save-movie-btn');

        foreach ($mediaTypeIds as $id) {
            $browser->uncheck("@media-type-{$id}");
        }

        $browser
            ->press('@save-movie-btn')
            ->waitForText('Cast Members')
            ->assertUrlIs(route('movies.show', $movie));

        expect($movie->refresh()->media_types)->toBeEmpty();
    });
});

it('does not reset the movie list after saving an edit on a movie loaded via infinite scroll', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser
            ->loginAs($user)
            ->visit(route('movies.index'))
            ->waitFor('[dusk^=movie-btn-]');

        // Scroll to the bottom repeatedly to load additional pages beyond the
        // initial 24 movies via infinite scroll.
        foreach (range(1, 6) as $_) {
            $browser->script('window.scrollTo(0, document.body.scrollHeight);');
            $browser->pause(600);
        }

        $posterCountBefore = $browser->script("return document.querySelectorAll('[dusk^=movie-btn-]').length;")[0];
        expect($posterCountBefore)->toBeGreaterThan(24);

        $lastDuskId = $browser->script("
            const buttons = document.querySelectorAll('[dusk^=movie-btn-]');
            return buttons[buttons.length - 1].getAttribute('dusk');
        ")[0];

        $browser
            ->click("[dusk=\"{$lastDuskId}\"]")
            ->waitForText('Cast Members')
            ->press('@edit-movie-btn')
            ->waitFor('@save-movie-btn')
            ->press('@save-movie-btn')
            ->waitForText('Cast Members')
            ->clickAtPoint(25, 25)
            ->waitUntilMissingModal()
            ->pause(500);

        $posterCountAfter = $browser->script("return document.querySelectorAll('[dusk^=movie-btn-]').length;")[0];

        expect($posterCountAfter)->toBeGreaterThanOrEqual($posterCountBefore);
    });
});
