<?php

namespace App\Jobs;

use App\Models\CastMember;
use App\Models\Movie;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;

class processMovieCastMembers implements ShouldQueue, ShouldBeUnique
{
    use Queueable, InteractsWithTMDB;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        private Movie $movie
        )
    {
        //
    }

    /**
     * Execute the job.
     *
     * movie credits (cast): https://api.themoviedb.org/3/movie/{movie_id}/credits { language }
     */
    public function handle(): void
    {
        $credits = $this->getMovieCast( $this->movie->id );

        foreach ( $credits->cast as $cast_member ) {
            $member = CastMember::firstOrCreate(
                [ 'id' => $cast_member->id ],
                [
                    'name' => $cast_member->name,
                    'original_name' => $cast_member->original_name,
                    'profile_path' => $cast_member->profile_path ?: ''
                ]
            );

            $this->movie->cast_members()->attach( $cast_member->id, [ 'character' => $cast_member->character, 'order' => $cast_member->order ] );
        }
    }
}
