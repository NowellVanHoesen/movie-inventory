<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\SeriesResource;
use App\Models\CastMember;
use App\Models\Series;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CastMemberController extends Controller
{
    // Display Cast Member and their associated movies, series, seasons, and episodes.
    public function __invoke(Request $request, CastMember $castMember) {
        $purchasedMemberMovies = $castMember->movies()->purchased()->orderBy('release_date', 'desc')->get();

        $wishlistedMemberMovies = $castMember->movies()->wishlist()->orderBy('release_date', 'desc')->get();

        $memberSeries = $castMember->series()->with( 'cast_members' )->get();

        $memberSeasons = $castMember->seasons()->with( 'series' )->get();

        foreach ( $memberSeasons as $season ) {
            $this->pushSeriesWithCharacter( $memberSeries, $season );
        }

        $memberEpisodes = $castMember->episodes()->with( 'series' )->get();

        foreach ( $memberEpisodes as $episode ) {
            $this->pushSeriesWithCharacter( $memberSeries, $episode );
        }

        $memberSeries = $memberSeries->unique('id')->sortBy('first_air_date');

        $pageTitle = config('app.name') . ' - Cast Member: ' . $castMember->name;

        return inertia('CastMembers/Index', [
            'purchasedMemberMovies' => MovieResource::collection( $purchasedMemberMovies ),
            'wishlistedMemberMovies' => MovieResource::collection( $wishlistedMemberMovies ),
            'memberSeries' => SeriesResource::collection( $memberSeries ),
            'page_title' => $pageTitle,
            'castMember' => $castMember,
        ]);
    }

    /**
     * Push a season's/episode's parent series onto the collection, copying the cast
     * member's character from the season/episode pivot onto the series so that
     * SeriesResource exposes it the same way it does for a directly-credited series.
     */
    private function pushSeriesWithCharacter(Collection $memberSeries, Model $pivotHolder): void
    {
        $series = $pivotHolder->series;

        if (! $series instanceof Series) {
            return;
        }

        $series = clone $series;
        $series->pivot_character = $pivotHolder->pivot->character;
        $series->syncOriginalAttribute('pivot_character');

        $memberSeries->push($series);
    }
}
