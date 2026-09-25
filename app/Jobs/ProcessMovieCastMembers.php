<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;

class ProcessMovieCastMembers implements ShouldBeUnique, ShouldQueue {
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

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        private Movie $movie
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * movie credits (cast): https://api.themoviedb.org/3/movie/{movie_id}/credits { language }
     */
    public function handle(): void {
        $credits = $this->requireTMDBResponse(
            $this->getMovieCast($this->movie->id),
            "movie {$this->movie->id} cast"
        );

        $this->attachCastMemberToModel($this->movie, $credits->cast);
    }

    public function uniqueId() {
        return "movie-{$this->movie->id}-cast";
    }
}
