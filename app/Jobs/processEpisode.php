<?php

namespace App\Jobs;

use App\Jobs\processEpisodeCastMembers;
use App\Models\Episode;
use App\Traits\InteractsWithTMDB;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processEpisode implements ShouldQueue
{
    use InteractsWithTMDB, Queueable, Batchable;

    protected int $series_id;

    protected int $season_id;

    protected int $season_number;

    protected int $episode_number;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args)
    {
        $this->series_id = $args['series_id'];
        $this->season_id = $args['season_id'];
        $this->season_number = $args['season_number'];
        $this->episode_number = $args['episode_number'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $episode_detail = $this->getEpisodeDetail($this->series_id, $this->season_number, $this->episode_number);

        $episode_record = Episode::firstOrCreate(
            ['id' => $episode_detail->id],
            [
                'imdb_id' => $episode_detail->external_ids->imdb_id ?? null,
                'name' => $episode_detail->name,
                'overview' => $episode_detail->overview,
                'still_path' => $episode_detail->still_path ?: null,
                'air_date' => $episode_detail->air_date ?: null,
                'runtime' => $episode_detail->runtime,
                'episode_number' => $episode_detail->episode_number,
                'season_id' => $this->season_id,
            ]
        );

        processEpisodeCastMembers::dispatch([
            'series_id' => $this->series_id,
            'season_number' => $this->season_number,
            'episode_id' => $episode_detail->id,
            'episode_number' => $this->episode_number
        ]);
    }

    public function uniqueId() {
        return "{$this->series_id}-{$this->season_id}-{$this->episode_number}-detail";
    }
}
