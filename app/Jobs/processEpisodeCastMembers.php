<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processEpisodeCastMembers implements ShouldBeUnique, ShouldQueue {
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

    protected int $episode_id;

    protected int $episode_number;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args) {
        $this->series_id = $args['series_id'];
        $this->season_number = $args['season_number'];
        $this->episode_id = $args['episode_id'];
        $this->episode_number = $args['episode_number'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // get Episode DB details
        $episode = Episode::firstWhere('id', $this->episode_id);

        $cast = $this->requireTMDBResponse(
            $this->getEpisodeCast($this->series_id, $this->season_number, $this->episode_number),
            "episode {$this->series_id}/s{$this->season_number}/e{$this->episode_number} cast"
        );

        $this->attachCastMemberToModel($episode, $cast->cast);

        if (! empty($cast->guest_stars)) {
            $this->attachCastMemberToModel($episode, $cast->guest_stars);
        }
    }

    public function uniqueId() {
        return "episode-{$this->episode_id}-cast";
    }
}
