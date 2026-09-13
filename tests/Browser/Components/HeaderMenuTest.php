<?php

use Database\Seeders\MoviesSeeder;
use Tests\Browser;

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('does not show the Add Movie link in the Movies menu when logged out', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->logout()
            ->visit(route('home'))
            ->waitFor('@nav-dropdown-Movies')
            ->press('@nav-dropdown-Movies')
            ->waitFor('@nav-link-movies.index')
            ->assertMissing('@nav-link-movies.create');
    });
});

it('shows the Add Movie link in the Movies menu when logged in, and navigates to the movie creation form', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(1)
            ->visit(route('home'))
            ->waitFor('@nav-dropdown-Movies')
            ->press('@nav-dropdown-Movies')
            ->waitFor('@nav-link-movies.create')
            ->press('@nav-link-movies.create')
            ->waitForLocation(route('movies.create'))
            ->waitFor('@movie-search-query-input')
            ->assertVisible('@movie-search-query-input');
    });
});
    });
});
