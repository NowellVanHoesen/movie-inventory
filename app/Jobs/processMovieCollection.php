<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Models\MovieCollection;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Support\Arr;

class processMovieCollection implements ShouldBeUnique, ShouldQueue {
    use InteractsWithTMDB, Queueable;

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

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        private int $collection_id
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        $collection = $this->requireTMDBResponse(
            $this->getMovieCollection($this->collection_id),
            "collection {$this->collection_id} detail"
        );

        $movieCollection = MovieCollection::firstOrCreate(
            ['id' => $collection->id],
            [
                'name' => $collection->name,
                'overview' => $collection->overview,
                'poster_path' => $collection->poster_path ?: null,
                'backdrop_path' => $collection->backdrop_path ?: null,
            ],
        );

        $movie_ids = Arr::pluck($collection->parts, 'id');

        Movie::whereIn('id', $movie_ids)
            ->update(['collection_id' => $movieCollection->id]);
    }

    public function uniqueId() {
        return "collection-{$this->collection_id}";
    }
}
