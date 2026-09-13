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

it('does not show the Add Series link in the Series menu when logged out', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->logout()
            ->visit(route('home'))
            ->waitFor('@nav-dropdown-Series')
            ->press('@nav-dropdown-Series')
            ->waitFor('@nav-link-series.index')
            ->assertMissing('@nav-link-series.create');
    });
});

it('shows the Add Series link in the Series menu when logged in, and navigates to the series creation form', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(1)
            ->visit(route('home'))
            ->waitFor('@nav-dropdown-Series')
            ->press('@nav-dropdown-Series')
            ->waitFor('@nav-link-series.create')
            ->press('@nav-link-series.create')
            ->waitForLocation(route('series.create'))
            ->waitFor('@series-search-query-input')
            ->assertVisible('@series-search-query-input');
    });
});
