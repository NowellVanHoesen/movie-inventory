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

class processMovieCollection implements ShouldBeUnique, ShouldQueue
{
    use InteractsWithTMDB, Queueable;

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
    public function handle(): void
    {
        $collection = $this->getMovieCollection($this->collection_id);

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
        return $this->collection_id;
    }
}
