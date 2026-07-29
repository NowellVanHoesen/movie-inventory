<?php

use App\Models\Movie;
use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
});

it('displays movie details, and credited cast members', function () {
    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    get(route('movies.show', $movie), ['X-Modal' => '1'])
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Movies/MovieModal')
            ->where('movie.title', $movie->title)
            ->where('movie.tagline', $movie->tagline)
            ->where('movie.overview', $movie->overview)
            ->where('movie.runtime', $movie->runtime)
            ->where('movie.cast_members.0.name', 'Jeremy Renner')
            ->where('movie.cast_members.0.pivot.character', 'Aaron Cross')
            ->where('movie.cast_members.1.name', 'Rachel Weisz')
            ->where('movie.cast_members.1.pivot.character', 'Dr. Marta Shearing')
            ->etc()
        );
});

it('does not display a link to edit the displayed movie when not logged in', function () {
    // A plain (non-modal) request is what the modal macro re-dispatches to the base
    // route with, and it's the only request that carries the shared `auth` prop the
    // Vue component gates the Edit button on (modal-only requests share no data —
    // see HandleInertiaRequests::share()).
    $movie = Movie::where('imdb_id', 'tt1194173')->first();

    get(route('movies.show', $movie))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('auth.user', null)
            ->where('_modal.component', 'Movies/MovieModal')
        );
});

it('provides the data needed for the inline edit form when logged in', function () {
    $movie = Movie::where('imdb_id', 'tt1194173')->first();
    $user = loginAsUser();

    get(route('movies.show', $movie))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('auth.user.id', $user->id)
            ->where('_modal.component', 'Movies/MovieModal')
            ->has('_modal.props.media_type_options')
            ->has('_modal.props.movie.media_types')
        );
});
