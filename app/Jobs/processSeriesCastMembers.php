<?php

namespace App\Jobs;

use App\Models\Series;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processSeriesCastMembers implements ShouldQueue
{
    use InteractsWithTMDB, CastMemberHelpers, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $series_id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get series DB detail
        $series = Series::firstWhere('id', $this->series_id);

        // get and attach Series cast members
        $series_cast = $this->getSeriesCast($this->series_id);

        foreach ($series_cast->cast as $cast_member) {
            if ( $series->cast_members()->where('cast_member_id', $cast_member->id)->exists() ) {
                continue;
            }

            $member = $this->getCastMember($cast_member);

            $series->cast_members()->attach($member->id, ['character' => $cast_member->character, 'order' => $cast_member->order]);
        }
    }

    public function uniqueId() {
        return "series-{$this->series_id}-cast";
    }
}
