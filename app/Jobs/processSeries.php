<?php

namespace App\Jobs;

use App\Jobs\processSeason;
use App\Jobs\processSeriesCastMembers;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class processSeries implements ShouldQueue
{
    use InteractsWithTMDB, Queueable;

    protected int $series_id;

    protected array $media_type;

    protected string $purchase_date;

    /**
     * Create a new job instance.
     */
    public function __construct(array $args)
    {
        $this->series_id = $args['series_id'];
        $this->media_type = $args['media_type'];
        $this->purchase_date = $args['purchase_date'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get series detail from API
        $series_detail = $this->getSeriesDetail($this->series_id);

        // set up chain/batch jobs
        $season_batch = [];

        foreach ($series_detail->seasons as $season) {
            $season_batch[] = new processSeason([
                'series_id' => $this->series_id,
                'media_type' => $this->media_type,
                'season_number' => $season->season_number,
                'purchase_date' => $this->purchase_date
            ]);
        }

        Bus::chain([
            new processSeriesCastMembers($this->series_id),
            Bus::batch( $season_batch )
        ])->dispatch();
    }
}
