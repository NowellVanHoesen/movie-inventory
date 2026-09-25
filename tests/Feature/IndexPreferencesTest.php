<?php

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Series;
use Database\Seeders\MoviesSeeder;
use Database\Seeders\SeriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * App\Traits\AppliesIndexPreferences reads FilterDropdown.vue's cookies, which are
 * unencrypted and client-writable. Both index pages must treat a tampered cookie
 * as "no preference" rather than 500ing.
 */
dataset('index pages', [
    'movies' => ['movies.index', MoviesSeeder::class, 'movies', ['genres' => 'selectedGenres', 'sortCol' => 'sortCol', 'sortDir' => 'sortDir']],
    'series' => ['series.index', SeriesSeeder::class, 'series', ['genres' => 'seriesSelectedGenres', 'sortCol' => 'seriesSortCol', 'sortDir' => 'seriesSortDir']],
]);

function indexIds($test, string $route, string $prop, array $cookies = []): array {
    return $test->withUnencryptedCookies($cookies)
        ->get(route($route))
        ->assertOk()
        ->viewData('page')['props'][$prop]['data'];
}

it('falls back to the default sort for an unknown sort column', function (string $route, string $seeder, string $prop, array $cookies) {
    $this->seed($seeder);

    $default = collect(indexIds($this, $route, $prop))->pluck('id')->all();
    $tampered = collect(indexIds($this, $route, $prop, [$cookies['sortCol'] => 'id; drop table users']))->pluck('id')->all();

    expect($tampered)->toBe($default)->not->toBeEmpty();
})->with('index pages');

it('ignores a genre cookie that is not a JSON array of strings', function (string $route, string $seeder, string $prop, array $cookies, string $genreCookie) {
    $this->seed($seeder);

    $unfiltered = count(indexIds($this, $route, $prop));

    expect(indexIds($this, $route, $prop, [$cookies['genres'] => $genreCookie]))->toHaveCount($unfiltered);
})->with('index pages')->with([
    'not json' => '{{{',
    'json scalar' => '"nope"',
    'array of non-strings' => '[1, {"a": 2}]',
]);

it('filters by the genres in the cookie', function (string $route, string $seeder, string $prop, array $cookies) {
    $this->seed($seeder);

    // Attach a brand-new genre to exactly one record, so the expected result is known
    // regardless of which genre links the seeder does or doesn't load.
    $modelClass = $prop === 'movies' ? Movie::class : Series::class;
    $record = $modelClass::firstOrFail();
    $genre = Genre::forceCreate(['id' => 990001, 'name' => 'Index Preferences Test Genre']);
    $record->genres()->attach($genre);

    $filtered = indexIds($this, $route, $prop, [$cookies['genres'] => json_encode([$genre->name])]);

    expect(collect($filtered)->pluck('id')->all())->toBe([$record->id]);
})->with('index pages');
