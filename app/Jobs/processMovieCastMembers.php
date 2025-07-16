<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;

class processMovieCastMembers implements ShouldBeUnique, ShouldQueue
{
    use InteractsWithTMDB, CastMemberHelpers, Queueable;

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
    public function handle(): void
    {
        $credits = $this->getMovieCast($this->movie->id);

        $this->attachCastMemberToModel( $this->movie, $credits->cast );
    }

    public function uniqueId() {
        return "movie-{$this->movie->id}-cast";
    }
}
