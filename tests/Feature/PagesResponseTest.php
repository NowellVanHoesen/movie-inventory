<?php

use function Pest\Laravel\get;

it('gives a successful response for the home page', function () {
    get(route('home'))
        ->assertOk();
});

it('loads the dashboard for an authenticated, verified user', function () {
    loginAsUser();

    get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Dashboard'));
});
