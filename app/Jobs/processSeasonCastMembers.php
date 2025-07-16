<?php

namespace App\Jobs;

use App\Models\Season;
use App\Traits\CastMemberHelpers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processSeasonCastMembers implements ShouldQueue
{
    use InteractsWithTMDB, CastMemberHelpers, Queueable;

    protected int $series_id;

    protected int $season_number;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args)
    {
        $this->series_id = $args['series_id'];
        $this->season_number = $args['season_number'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get Season DB detail
        $season = Season::where('series_id', $this->series_id)->where('season_number', $this->season_number)->first();

        // get and attach Season cast members
        $cast = $this->getSeasonCast($this->series_id, $this->season_number);

        $this->attachCastMemberToModel( $season, $cast->cast );
    }

    public function uniqueId() {
        return "season-{$this->series_id}-{$this->season_number}-cast";
    }
}
