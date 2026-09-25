<?php

namespace App\Jobs;

use App\Models\Season;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSeasonCastMembers implements ShouldBeUnique, ShouldQueue {
    use CastMemberHelpers, InteractsWithTMDB, Queueable;

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

    protected int $season_number;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args) {
        $this->series_id = $args['series_id'];
        $this->season_number = $args['season_number'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // get Season DB detail
        $season = Season::where('series_id', $this->series_id)->where('season_number', $this->season_number)->first();

        // get and attach Season cast members
        $cast = $this->requireTMDBResponse(
            $this->getSeasonCast($this->series_id, $this->season_number),
            "series {$this->series_id} season {$this->season_number} cast"
        );

        $this->attachCastMemberToModel($season, $cast->cast);
    }

    public function uniqueId() {
        return "season-{$this->series_id}-{$this->season_number}-cast";
    }
}
