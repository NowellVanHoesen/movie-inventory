<?php

namespace App\Http\Controllers;

use App\Models\CastMember;

class CastMemberController extends Controller
{
    // Display Cast Member and their associated movies, series, seasons, and episodes.
    public function show(CastMember $castMember)
    {
        $memberMovies = $castMember->movies()->orderBy('release_date', 'desc')->get();

        $memberSeries = $castMember->series()->get();

        $memberSeasons = $castMember->seasons()->with( 'series' )->get();

        foreach ( $memberSeasons as $season ) {
            $memberSeries->push( $season->series );
        }

        $memberEpisodes = $castMember->episodes()->with( 'series' )->get();

        foreach ( $memberEpisodes as $episode ) {
            $memberSeries->push( $episode->series );
        }

        $memberSeries = $memberSeries->unique('id')->sortBy('first_air_date');

        $pageTitle = config('app.name') . ' - Cast Member: ' . $castMember->name;

        return view('castMembers.show', compact('castMember', 'memberMovies', 'memberSeries', 'pageTitle'));
    }
}
