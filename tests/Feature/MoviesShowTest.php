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


