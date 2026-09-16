<?php

namespace App\Jobs;

use App\Models\Series;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processSeriesCastMembers implements ShouldBeUnique, ShouldQueue {
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

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $series_id) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // get series DB detail
        $series = Series::firstWhere('id', $this->series_id);

        // get and attach Series cast members
        $series_cast = $this->requireTMDBResponse(
            $this->getSeriesCast($this->series_id),
            "series {$this->series_id} cast"
        );

        $this->attachCastMemberToModel($series, $series_cast->cast);
    }

    public function uniqueId() {
        return "series-{$this->series_id}-cast";
    }
}
