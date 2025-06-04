<?php

namespace App\Jobs;

use App\Models\CastMember;
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

        foreach ($credits->cast as $cast_member) {
            if ( $this->movie->cast_members()->where('cast_member_id', $cast_member->id)->exists() ) {
                continue;
            }

            $member = $this->getCastMember($cast_member);

            $this->movie->cast_members()->attach($cast_member->id, ['character' => $cast_member->character, 'order' => $cast_member->order]);
        }
    }

    public function uniqueId() {
        return "movie-{$this->movie->id}-cast";
    }
}
