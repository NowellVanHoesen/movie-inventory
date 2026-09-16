<?php

namespace App\Jobs;

use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class processSeries implements ShouldBeUnique, ShouldQueue {
    use InteractsWithTMDB, Queueable;

    /**
     * Retry transient TMDB failures before giving up — previously a single
     * rate-limit or timeout permanently lost that ingestion with no retry.
     */
    public int $tries = 3;

    public array $backoff = [10, 30];

    /**
     * Bound the uniqueness lock so a hard worker crash can't wedge this job id.
     */
    public int $uniqueFor = 3600;

    protected int $series_id;

    protected array $media_type;

    // Non-nullable on purpose: series have no wishlist concept (unlike Movie, which
    // has purchased()/wishlist() scopes and routes), so a series always has a
    // purchase date. SeriesController@store enforces that with a `required` rule.
    protected string $purchase_date;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args) {
        $this->series_id = $args['series_id'];
        $this->media_type = $args['media_type'] ?? [];
        $this->purchase_date = $args['purchase_date'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // get series detail from API
        $series_detail = $this->requireTMDBResponse(
            $this->getSeriesDetail($this->series_id),
            "series {$this->series_id} detail"
        );

        // set up chain/batch jobs
        $season_batch = [];

        foreach ($series_detail->seasons as $season) {
            $season_batch[] = new processSeason([
                'series_id' => $this->series_id,
                'media_type' => $this->media_type,
                'season_number' => $season->season_number,
                'purchase_date' => $this->purchase_date,
            ]);
        }

        Bus::chain([
            new processSeriesCastMembers($this->series_id),
            Bus::batch($season_batch),
        ])->dispatch();
    }

    public function uniqueId() {
        return "series-{$this->series_id}";
    }
}
