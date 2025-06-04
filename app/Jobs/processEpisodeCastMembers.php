<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processEpisodeCastMembers implements ShouldQueue
{
    use InteractsWithTMDB, CastMemberHelpers, Queueable;

    protected int $series_id;

    protected int $season_number;

    protected int $episode_id;

    protected int $episode_number;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args)
    {
        $this->series_id = $args['series_id'];
        $this->season_number = $args['season_number'];
        $this->episode_id = $args['episode_id'];
        $this->episode_number = $args['episode_number'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get Episode DB details
        $episode = Episode::firstWhere('id', $this->episode_id);

        $cast = $this->getEpisodeCast($this->series_id, $this->season_number, $this->episode_number);

        foreach ($cast->cast as $cast_member) {
            if ( $episode->cast_members()->where('cast_member_id', $cast_member->id)->exists() ) {
                continue;
            }

            $member = $this->getCastMember($cast_member);

            $episode->cast_members()->attach($member->id, ['character' => $cast_member->character, 'order' => $cast_member->order]);
        }

        if (! empty($cast->guest_stars)) {
            foreach ($cast->guest_stars as $cast_member) {
                $member = $this->getCastMember($cast_member);

                $episode->cast_members()->attach($member->id, ['character' => $cast_member->character, 'order' => $cast_member->order]);
            }
        }
    }

    public function uniqueId() {
        return "episode-{$this->episode_id}-cast";
    }
}
