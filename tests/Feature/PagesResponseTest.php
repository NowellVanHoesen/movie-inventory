<?php

use function Pest\Laravel\get;

it('gives a successful response for the home page', function () {
    get(route('home'))
        ->assertOk();
});
