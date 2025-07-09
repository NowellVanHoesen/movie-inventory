<?php

use App\Models\Movie;
use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays movie details, and credited cast members', function () {
    $this->seed(MoviesSeeder::class);
    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    get(route('movies.show', $movie))
        ->assertOk()
        ->assertSee([
            $movie->title,
            $movie->tagline,
            $movie->overview,
            "{$movie->runtime} min."
        ])
        ->assertDontSee([
            'Paddy Considine',
            'Sheena Colette',
            'Alexis Molnar',
            'Courtney Hart',
        ])->assertSeeInOrder([
            'Jeremy Renner',
            'Aaron Cross',
            'Rachel Weisz',
            'Dr. Marta Shearing',
        ]);
});

it('does not display a link to edit the displayed movie when not logged in', function() {
    $this->seed(MoviesSeeder::class);
    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    get(route('movies.show', $movie))
        ->assertOk()
        ->assertDontSeeText('Edit')
        ->assertDontSee(route('movies.edit', $movie));
});

it('displays a link to edit the displayed movie when logged in', function() {
    $this->seed(MoviesSeeder::class);
    $movie = Movie::where('imdb_id', 'tt1194173')->first();
    loginAsUser();

    get(route('movies.show', $movie))
        ->assertOk()
        ->assertSeeText('Edit')
        ->assertSee(route('movies.edit', $movie));
});
