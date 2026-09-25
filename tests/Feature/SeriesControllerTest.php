<?php

use App\Jobs\ProcessSeries;
use App\Models\Certification;
use App\Models\Episode;
use App\Models\Season;
use App\Models\Series;
use Database\Seeders\SeriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

describe('SeriesController', function () {
    beforeEach(function () {
        // Setup common data
        // Certification::factory()->create(['name' => 'NR']);
    });

    it('shows the series index', function () {
        $this->seed(SeriesSeeder::class);
        $response = $this->get(route('series.index'));
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Series/Index')
                ->has('series')
                ->etc()
        );
    });

    it('shows the create form with query', function () {
        loginAsUser();

        $response = $this->get(route('series.create', ['query' => 'Heroes Reborn']));
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Series/Create')
                ->where('search_term', 'Heroes Reborn')
                ->has('search_results')
                ->has('local_results')
                ->etc()
        );
    });

    it('flags a search result as already owned when it matches a local series', function () {
        loginAsUser();
        $this->seed(SeriesSeeder::class);

        $response = $this->get(route('series.create', ['query' => 'Dexter']));
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Series/Create')
                ->has('search_results')
                ->has('local_results', 1)
                ->where('local_results.0.id', 1405)
                ->etc()
        );
    });

    it('shows the create form with series_id', function () {
        loginAsUser();

        $response = $this->get(route('series.create', ['series_id' => 60858, 'search_term' => 'Heroes Reborn']));
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Series/Create')
                ->has('series_detail')
                ->has('media_types')
                ->etc()
        );
    });

    it('validates store request data', function () {
        loginAsUser();

        $response = $this->post(route('series.store'), [
            'series_id' => 'not-an-integer',
            'purchase_date' => 'invalid-date',
            'media_type' => 'not-an-array',
            'season_numbers' => 'not-an-array',
        ]);
        $response->assertSessionHasErrors(['series_id', 'purchase_date', 'media_type', 'season_numbers']);
    });

    it('stores a new series and dispatches job', function () {
        loginAsUser();

        Queue::fake();
        $payload = [
            'series_id' => 60858,
            'purchase_date' => '2025-02-04',
            'media_type' => [3],
        ];
        // Mock getSeriesDetail, etc. if needed
        $response = $this->post(route('series.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('series', ['id' => 60858]);
        Queue::assertPushed(ProcessSeries::class);
    });

    it('shows a series detail page', function () {
        $this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();
        $response = $this->get(route('series.show', $series));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('Series/Show')
                ->where('series.slug', $series->slug)
                ->etc()
        );
    });

    // Season and episode detail no longer have their own routes/pages — they're
    // shown by selecting a season/episode within Series/Show.vue, so the series
    // detail response needs to carry the full seasons -> episodes tree up front.
    it('includes seasons and episodes in the series detail page', function () {
        $this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();
        $season = Season::where(['series_id' => $series->id, 'season_number' => 1])->firstOrFail();
        $episode = Episode::where(['season_id' => $season->id, 'episode_number' => 4])->firstOrFail();

        $response = $this->get(route('series.show', $series));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('Series/Show')
                ->has('series.seasons', $series->seasons->count())
                ->has(
                    'series.seasons.0',
                    fn ($seasonJson) => $seasonJson
                        ->where('id', $season->id)
                        ->has('episodes', $season->episodes->count())
                        ->has(
                            'episodes.3',
                            fn ($episodeJson) => $episodeJson
                                ->where('id', $episode->id)
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
    });

    it('leaves episode cast out of the initial series payload', function () {
        $this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();

        $this->get(route('series.show', $series))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->missing('episode_cast')
                    ->missing('series.seasons.0.episodes.0.cast_members')
                    ->etc()
            );
    });

    it('returns one episode\'s cast on the episode_cast partial reload', function () {
        $this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();
        $episode = $series->episodes()->has('cast_members')->firstOrFail();

        $this->get(route('series.show', ['series' => $series, 'episode' => $episode->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->reloadOnly(
                    'episode_cast',
                    fn ($reload) => $reload
                        ->where('episode_cast.episode_id', $episode->id)
                        ->has('episode_cast.cast_members', $episode->cast_members()->count())
                        ->has('episode_cast.cast_members.0.pivot.character')
                )
            );
    });

    it('does not return cast for an episode that belongs to a different series', function () {
        $this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();
        $otherEpisode = Episode::whereHas('season', fn ($q) => $q->where('series_id', '!=', $series->id))
            ->has('cast_members')
            ->firstOrFail();

        $this->get(route('series.show', ['series' => $series, 'episode' => $otherEpisode->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->reloadOnly(
                    'episode_cast',
                    fn ($reload) => $reload
                        ->where('episode_cast.episode_id', null)
                        ->has('episode_cast.cast_members', 0)
                )
            );
    });

    it('returns validation errors for missing required fields', function () {
        loginAsUser();

        $response = $this->post(route('series.store'), ['series_id' => null, 'media_type' => null, 'season_numbers' => null]);
        $response->assertSessionHasErrors(['series_id', 'media_type', 'season_numbers']);
    });

    // Series have no wishlist (unlike movies, which have purchased()/wishlist()
    // scopes and dedicated routes), so a series is always owned and must carry a
    // purchase date. This used to be a `nullable` rule, and the resulting null
    // reached ProcessSeries' typed property and 500'd with a TypeError.
    it('rejects a series with no purchase date, since series have no wishlist', function () {
        loginAsUser();

        $response = $this->post(route('series.store'), [
            'series_id' => 60858,
            'purchase_date' => null,
            'media_type' => [],
        ]);

        $response->assertSessionHasErrors('purchase_date');
        $this->assertDatabaseMissing('series', ['id' => 60858]);
    });

    it('requires authentication to store a series', function () {
        $response = $this->post(route('series.store'), [
            'series_id' => 60858,
            'purchase_date' => '2025-02-04',
            'media_type' => [3],
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('series', ['id' => 60858]);
    });

    it('uses the first US content rating, falling back to NR when it is not one we seed', function (array $ratings, string $expected) {
        loginAsUser();
        Queue::fake();
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/tv/900002*' => Http::response([
                'id' => 900002,
                'external_ids' => ['imdb_id' => null],
                'name' => 'Rated Show',
                'original_name' => 'Rated Show',
                'tagline' => '',
                'overview' => '',
                'homepage' => '',
                'poster_path' => null,
                'backdrop_path' => null,
                'first_air_date' => '2020-01-01',
                'genres' => [],
                'content_ratings' => ['results' => $ratings],
            ]),
        ]);

        $this->post(route('series.store'), [
            'series_id' => 900002,
            'purchase_date' => '2025-02-04',
            'media_type' => [],
        ])->assertRedirect();

        $this->assertDatabaseHas('series', [
            'id' => 900002,
            'certification_id' => Certification::where('name', $expected)->value('id'),
        ]);
    })->with([
        'unseeded rating' => [[['iso_3166_1' => 'US', 'rating' => 'TV-Y7-FV']], 'NR'],
        'first US rating wins' => [[['iso_3166_1' => 'US', 'rating' => 'TV-14'], ['iso_3166_1' => 'US', 'rating' => 'TV-MA']], 'TV-14'],
        'no US rating' => [[['iso_3166_1' => 'GB', 'rating' => '15']], 'NR'],
    ]);

    it('treats LIKE wildcards in the create search as literal characters', function () {
        loginAsUser();
        $this->seed(SeriesSeeder::class);
        Http::preventStrayRequests();
        Http::fake(['api.themoviedb.org/*' => Http::response(['results' => []])]);

        $this->get(route('series.create', ['query' => '%%']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('local_results', 0)->etc());
    });

    // Add more edge case tests as needed
});
