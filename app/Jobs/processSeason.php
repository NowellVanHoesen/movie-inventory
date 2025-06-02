<?php

namespace App\Jobs;

use App\Jobs\processEpisode;
use App\Jobs\processSeasonCastMembers;
use App\Models\Season;
use App\Traits\InteractsWithTMDB;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class processSeason implements ShouldQueue
{
    use InteractsWithTMDB, Queueable, Batchable;

    protected int $series_id;

    protected array $media_type;

    protected int $season_number;

    protected string $purchase_date;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args)
    {
        $this->series_id = $args['series_id'];
        $this->media_type = $args['media_type'];
        $this->season_number = $args['season_number'];
        $this->purchase_date = $args['purchase_date'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get and save Season detail
        $season_detail = $this->getSeasonDetail($this->series_id, $this->season_number);

        $season_record = Season::firstOrCreate(
            ['id' => $season_detail->id],
            [
                '_id' => $season_detail->_id,
                'series_id' => $this->series_id,
                'imdb_id' => $season_detail->external_ids->imdb_id ?? null,
                'name' => $season_detail->name,
                'overview' => $season_detail->overview,
                'air_date' => $season_detail->air_date,
                'purchase_date' => $this->purchase_date,
                'season_number' => $season_detail->season_number,
                'poster_path' => $season_detail->poster_path ?: null,
            ]
        );

        // get and attach media type(s)
        if (! empty($this->media_type)) {
            foreach ($this->media_type as $media_type_id) {
                $season_record->media_types()->attach($media_type_id);
            }
        }

        $episode_batch = [];

         foreach ($season_detail->episodes as $episode) {
            $episode_batch[] = new processEpisode([
                'series_id' => $this->series_id,
                'season_id' => $season_detail->id,
                'season_number' => $this->season_number,
                'episode_number' => $episode->episode_number
            ]);
         }

        // set up job chain and episode batch
        Bus::chain([
            new processSeasonCastMembers([
                'series_id' => $this->series_id,
                'season_number' => $this->season_number
            ]),
            Bus::batch( $episode_batch )
        ])->dispatch();
    }
}
