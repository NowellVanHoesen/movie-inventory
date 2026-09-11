<?php

use Database\Seeders\MoviesSeeder;
use Tests\Browser;

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('does not show the Add Movie link in the Movies menu when logged out', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->visit(route('home'))
            ->waitFor('@nav-dropdown-Movies')
            ->press('@nav-dropdown-Movies')
            ->waitFor('@nav-link-movies.index')
            ->assertMissing('@nav-link-movies.create');
    });
});

// movies.create is still a legacy Blade page (not yet converted to Inertia/Vue), so
// clicking this Inertia <Link> doesn't perform a normal navigation the browser can wait
// on. Once movies.create is converted, replace this with a click-through assertion like
// the one in the "logged out" test above (waitFor + click + waitForLocation/assertPathIs).
it('shows the Add Movie link in the Movies menu when logged in, pointing at the movie creation form', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(1)
            ->visit(route('home'))
            ->waitFor('@nav-dropdown-Movies')
            ->press('@nav-dropdown-Movies')
            ->waitFor('@nav-link-movies.create')
            ->assertVisible('@nav-link-movies.create')
            ->assertAttribute('@nav-link-movies.create', 'href', route('movies.create'));
    });
});
