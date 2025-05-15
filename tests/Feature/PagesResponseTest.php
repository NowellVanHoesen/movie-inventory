<?php

use function Pest\Laravel\get;

it('gives a successful response for the home page', function () {
    get(route('home'))
        ->assertOk();
});

it('gives a successful response for the about page', function () {
    get(route('about'))
        ->assertOk();
});

it('gives a successful response for the contact page', function () {
    get(route('contact'))
        ->assertOk();
});
