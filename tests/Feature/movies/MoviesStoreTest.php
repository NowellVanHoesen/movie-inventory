<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('requires authentication to store a movie', function () {
    post(route('movies.store'), [
        'movie_id' => 27205,
        'purchase_date' => '2025-02-04',
        'media_type' => [],
    ])->assertRedirect(route('login'));

    $this->assertDatabaseMissing('movies', ['id' => 27205]);
});
