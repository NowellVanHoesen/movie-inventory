<?php

use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\processSeries;
use Database\Seeders\SeriesSeeder;

uses(RefreshDatabase::class);

describe('SeriesController', function () {
    beforeEach(function () {
        // Setup common data
        //Certification::factory()->create(['name' => 'NR']);
    });

    it('shows the series index', function () {
		$this->seed(SeriesSeeder::class);
        $response = $this->get(route('series.index'));
        $response->assertOk();
        $response->assertViewHas('series');
    });

    it('shows the create form with query', function () {
		loginAsUser();

        $response = $this->get(route('series.create', ['query' => 'Heroes Reborn']));
        $response->assertOk();
        $response->assertViewHas('search_term', 'Heroes Reborn');
    });

    it('shows the create form with series_id', function () {
	    loginAsUser();

        $response = $this->get(route('series.create', ['series_id' => 60858, 'search_term' => 'Heroes Reborn']));
        $response->assertOk();
        $response->assertViewHas('series_detail');
    });

    it('validates store request data', function () {
        $response = $this->post(route('series.store'), [
            'series_id' => 'not-an-integer',
            'purchase_date' => 'invalid-date',
            'media_type' => 'not-an-array',
            'season_numbers' => 'not-an-array',
        ]);
        $response->assertSessionHasErrors(['series_id', 'purchase_date', 'media_type', 'season_numbers']);
    });

    it('stores a new series and dispatches job', function () {
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
        Queue::assertPushed(processSeries::class);
    });

    it('shows a series detail page', function () {
		$this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();
        $response = $this->get(route('series.show', $series));
        $response->assertStatus(200);
        $response->assertViewHas('series');
    });

    it('shows season and episode pages', function () {
		$this->seed(SeriesSeeder::class);
        $series = Series::where('slug', 'the-flight-attendant')->firstOrFail();
        $season = Season::where(['series_id' => $series->id, 'season_number' => 1])->firstOrFail();
        $episode = Episode::where(['season_id' => $season->id, 'episode_number' => 4])->firstOrFail();
        $response = $this->get(route('season.show', [$series, $season]));
        $response->assertStatus(200);
        $response = $this->get(route('episode.show', [$series, $season, $episode]));
        $response->assertStatus(200);
    });

    it('returns validation errors for missing required fields', function () {
        $response = $this->post(route('series.store'), ['series_id' => null, 'media_type' => null, 'season_numbers' => null]);
        $response->assertSessionHasErrors(['series_id', 'media_type', 'season_numbers']);
    });

    it('handles exception in getSeriesDetail', function () {
        // Simulate getSeriesDetail throws exception
        // ...mocking logic here...
        // ...assertions for error response...
    });

    // Add more edge case tests as needed
});
