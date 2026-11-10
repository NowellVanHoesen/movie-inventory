<?php

use App\Models\Movie;
use App\Models\User;
use Database\Seeders\MoviesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\withHeaders;

uses(RefreshDatabase::class);

// The modal macro renders a "background" route chosen by the client-supplied
// X-Modal-Base-Url / Referer header, and runs it without its route middleware.
// These guard against that header reaching auth-only or modal routes.

beforeEach(function () {
    $this->seed(MoviesSeeder::class);
    $this->movie = Movie::where('imdb_id', 'tt1194173')->firstOrFail();
});

it('does not render an auth-only background for a guest', function () {
    Http::preventStrayRequests();
    Http::fake();

    withHeaders(['X-Modal-Base-Url' => '/movies/create?query=batman'])
        ->get(route('movies.show', $this->movie))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Movies/Index')
                ->where('_modal.component', 'Movies/MovieModal')
                ->where('_modal.baseUrl', route('movies.index'))
                ->etc()
        );

    Http::assertNothingSent();
});

it('renders an auth-only background for a logged-in user, with its own query string', function () {
    loginAsUser();
    Http::preventStrayRequests();
    Http::fake(['api.themoviedb.org/3/search/movie*' => Http::response(['results' => []])]);

    withHeaders(['X-Modal-Base-Url' => '/movies/create?query=bourne'])
        ->get(route('movies.show', $this->movie))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Movies/Create')
                ->where('search_term', 'bourne')
                ->where('_modal.component', 'Movies/MovieModal')
                ->etc()
        );
});

it('does not recurse when the base url is another modal route', function (string $modalRoute, string $base) {
    $url = $modalRoute === 'login' ? route('login') : route('movies.show', $this->movie);

    withHeaders(['X-Modal-Base-Url' => $base])
        ->get($url)
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('_modal.baseUrl', fn ($baseUrl) => $baseUrl !== $base)->etc());
})->with([
    'movie over another movie' => ['movies.show', '/movies/some-other-movie'],
    'login over a movie' => ['login', '/movies/the-bourne-legacy'],
    'login over itself' => ['login', '/login'],
]);

it('falls back instead of 404ing when the base url matches no route', function () {
    withHeaders(['X-Modal-Base-Url' => '/does-not-exist'])
        ->get(route('movies.show', $this->movie))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Movies/Index')->etc());
});

it('ignores an off-origin base url', function (string $header, string $value) {
    withHeaders([$header => $value])
        ->get(route('movies.show', $this->movie))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Movies/Index')
                ->where('_modal.baseUrl', route('movies.index'))
                ->etc()
        );
})->with([
    'absolute header' => ['X-Modal-Base-Url', 'https://evil.example/'],
    'protocol-relative header' => ['X-Modal-Base-Url', '//evil.example/'],
    'external referer' => ['Referer', 'https://evil.example/'],
]);

it('gives the background route its own query string', function () {
    withHeaders(['X-Modal-Base-Url' => '/search?search=Bourne'])
        ->get(route('movies.show', $this->movie))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Search/Index')
                ->where('search', 'Bourne')
                ->where('movies', fn ($movies) => collect($movies)->pluck('id')->contains($this->movie->id))
                ->etc()
        );
});

it('applies the purchased filter when that list is the background', function () {
    withHeaders(['X-Modal-Base-Url' => route('movies.purchased')])
        ->get(route('movies.show', $this->movie))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Movies/Index')
                ->where('page_title', config('app.name') . ' - Purchased Movies')
                ->etc()
        );
});

it('does not redirect off-site after logging in from a modal', function () {
    $user = User::factory()->create();

    withHeaders(['X-Modal-Base-Url' => 'https://evil.example/phish'])
        ->post(route('login'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('home'));
});
