<?php

namespace App\Jobs;

use App\Models\CastMember;
use App\Models\Episode;
use App\Models\Season;
use App\Models\Series;
use App\Traits\InteractsWithTMDB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class processSeries implements ShouldQueue
{
    use Queueable, InteractsWithTMDB;

    protected int $series_id;

    protected array $media_type;

    protected array $season_numbers;

    protected string $purchase_date;

    /**
     * Create a new job instance.
     */
    public function __construct( array $args )
    {
        $this->series_id = $args['series_id'];
        $this->media_type = $args['media_type'];
        $this->season_numbers = $args['season_numbers'];
        $this->purchase_date = $args['purchase_date'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get Series detail
        $series = Series::firstWhere( 'id', $this->series_id );

        $series_detail = $this->getSeriesDetail( $this->series_id );

        // get and attach Series cast members
        $series_cast = $this->getSeriesCast( $series->id );

        foreach ( $series_cast->cast as $cast_member ) {
            $member = $this->getCastMember( $cast_member );

            $series->cast_members()->attach( $cast_member->id, [ 'character' => $cast_member->character, 'order' => $cast_member->order ] );
        }

        // loop over seasons
        foreach ( $series_detail->seasons as $season ) {
            // get and save season detail - set purchase date for all unless $args[season_number] has list of selected, then only set date on the selected
            $season_detail = $this->getSeasonDetail( $this->series_id, $season->season_number );

            $season_record = Season::firstOrCreate(
                [ 'id' => $season->id ],
                [
                    '_id' => $season_detail->_id,
                    'series_id' => $this->series_id,
                    'imdb_id' => $season_detail->external_ids->imdb_id ?? null,
                    'name' => $season_detail->name,
                    'overview' => $season_detail->overview,
                    'air_date' => $season_detail->air_date,
                    'purchase_date' => $this->purchase_date,
                    'season_number' => $season_detail->season_number,
                    'poster_path' => $season_detail->poster_path ?? '',
                ]
            );

            // get and attach media type(s)
            if ( ! empty( $this->media_type ) ) {
                foreach ( $this->media_type as $media_type_id ) {
                    $season_record->media_types()->attach( $media_type_id );
                }
            }

            // get and attach Season cast members
            $this->processSeasonCastMembers( $season_record );

            // loop over episodes
            foreach ( $season_detail->episodes as $episode ) {
                // get and save episode detail
                $episode_detail = $this->getEpisodeDetail( $this->series_id, $season_record->season_number, $episode->episode_number );

                $episode_record = Episode::firstOrCreate(
                    [ 'id' => $episode_detail->id ],
                    [
                        'imdb_id' => $episode_detail->external_ids->imdb_id ?? null,
                        'name' => $episode_detail->name,
                        'overview' => $episode_detail->overview,
                        'still_path' => $episode_detail->still_path ?? '',
                        'air_date' => $episode_detail->air_date ?? null,
                        'runtime' => $episode_detail->runtime,
                        'episode_number' => $episode_detail->episode_number,
                        'season_id' => $season->id,
                    ]
                );
                // get and attach guest cast
                $this->processEpisodeCastMembers( $episode_record, $season_record );
            }
        }
    }

    private function processSeasonCastMembers( Season $season ) {
        $cast = $this->getSeasonCast( $season->series_id, $season->season_number );

        foreach ( $cast->cast as $cast_member ) {
            $member = $this->getCastMember( $cast_member );

            $season->cast_members()->attach( $cast_member->id, [ 'character' => $cast_member->character, 'order' => $cast_member->order ] );
        }
    }

    private function processEpisodeCastMembers( Episode $episode, Season $season ) {
        $cast = $this->getSeasonCast( $this->series_id, $season->season_number, $episode->episode_number );

        foreach ( $cast->cast as $cast_member ) {
            $member = $this->getCastMember( $cast_member );

            $episode->cast_members()->attach( $cast_member->id, [ 'character' => $cast_member->character, 'order' => $cast_member->order ] );
        }

        if ( ! empty( $cast->guest_stars ) ) {
            foreach ( $cast->guest_stars as $cast_member ) {
                $member = $this->getCastMember( $cast_member );

                $episode->cast_members()->attach( $cast_member->id, [ 'character' => $cast_member->character, 'order' => $cast_member->order ] );
            }
        }
    }

    private function getCastMember( $cast_member ) {
        return CastMember::firstOrCreate(
            [ 'id' => $cast_member->id ],
            [
                'name' => $cast_member->name,
                'original_name' => $cast_member->original_name,
                'profile_path' => $cast_member->profile_path ?: ''
            ]
        );
    }
}
