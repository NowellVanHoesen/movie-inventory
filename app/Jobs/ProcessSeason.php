<?php

namespace App\Jobs;

use App\Models\Season;
use App\Traits\InteractsWithTMDB;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class ProcessSeason implements ShouldBeUnique, ShouldQueue {
    use Batchable, InteractsWithTMDB, Queueable;

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

    protected int $season_number;

    // Non-nullable on purpose: series have no wishlist concept, so a series — and
    // therefore every season of it — always has a purchase date.
    protected string $purchase_date;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args) {
        $this->series_id = $args['series_id'];
        $this->media_type = $args['media_type'] ?? [];
        $this->season_number = $args['season_number'];
        $this->purchase_date = $args['purchase_date'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // get and save Season detail
        $season_detail = $this->requireTMDBResponse(
            $this->getSeasonDetail($this->series_id, $this->season_number),
            "series {$this->series_id} season {$this->season_number} detail"
        );

        $season_record = Season::firstOrCreate(
            ['id' => $season_detail->id],
            [
                '_id' => $season_detail->_id,
                'series_id' => $this->series_id,
                'imdb_id' => $season_detail->external_ids->imdb_id ?? null,
                'name' => $season_detail->name,
                'overview' => $season_detail->overview,
                'air_date' => $season_detail->air_date,
                'purchase_date' => $this->purchase_date,
                'season_number' => $season_detail->season_number,
                'poster_path' => $season_detail->poster_path ?: null,
            ]
        );

        // syncWithoutDetaching, not attach: the season above comes from firstOrCreate,
        // so on a retry or a re-added series these rows already exist and the pivot's
        // composite primary key turns a second attach() into an integrity violation.
        if (! empty($this->media_type)) {
            $season_record->media_types()->syncWithoutDetaching($this->media_type);
        }

        $episode_batch = [];

        foreach ($season_detail->episodes as $episode) {
            $episode_batch[] = new ProcessEpisode([
                'series_id' => $this->series_id,
                'season_id' => $season_detail->id,
                'season_number' => $this->season_number,
                'episode_number' => $episode->episode_number,
            ]);
        }

        // set up job chain and episode batch
        Bus::chain([
            new ProcessSeasonCastMembers([
                'series_id' => $this->series_id,
                'season_number' => $this->season_number,
            ]),
            Bus::batch($episode_batch),
        ])->dispatch();
    }

    public function uniqueId() {
        return "season-{$this->series_id}-{$this->season_number}-detail";
    }
}
