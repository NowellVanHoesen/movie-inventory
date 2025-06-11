<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait InteractsWithTMDB
{
    private function getMovieDetail(int $movie_id)
    {
        return $this->sendTMDBRequest(
            "movie/{$movie_id}",
            [
                'language' => 'en-US',
                'append_to_response' => 'release_dates',
            ]
        );
    }

    private function getMovieCollection(int $collection_id)
    {
        return $this->sendTMDBRequest(
            "collection/{$collection_id}",
            [
                'language' => 'en-US'
            ]
        );
    }

    private function getMovieCast(int $movie_id)
    {
        return $this->sendTMDBRequest(
            "movie/{$movie_id}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function getMovieImages(int $movie_id) {
        return $this->sendTMDBRequest(
            "movie/{$movie_id}/images"
        );
    }

    private function getCollectionImages(int $collection_id) {
        return $this->sendTMDBRequest(
            "collection/{$collection_id}/images"
        );
    }

    private function getMovieRecommendations(int $movie_id) {
        $recs = $this->sendTMDBRequest(
            "movie/{$movie_id}/recommendations"
        );

        $allRecs = collect( $recs->results );

        if ( $recs->total_pages > 1 ) {
            for ($page = 2; $page <= $recs->total_pages; $page++ ) {
                $pagedRecs = $allRecs->merge( collect( $this->sendTMDBRequest( "movie/{$movie_id}/recommendations", [ 'page' => $page ] )->results ) );
                $allRecs = $pagedRecs;
            }
        }

        return $allRecs;
    }

    private function getSeriesRecommendations(int $series_id) {
        $recs = $this->sendTMDBRequest(
            "tv/{$series_id}/recommendations"
        );

        $allRecs = collect( $recs->results );

        if ( $recs->total_pages > 1 ) {
            for ($page = 2; $page <= $recs->total_pages; $page++ ) {
                $pagedRecs = $allRecs->merge( collect( $this->sendTMDBRequest( "tv/{$series_id}/recommendations", [ 'page' => $page ] )->results ) );
                $allRecs = $pagedRecs;
            }
        }

        return $allRecs;
    }

    private function searchMovies(string $query, int $page = 1)
    {
        return $this->sendTMDBRequest(
            'search/movie',
            [
                'query' => $query,
                'include_adult' => true,
                'language' => 'en-US',
                'page' => $page,
            ]
        );
    }

    private function searchSeries(string $query, int $page = 1)
    {
        return $this->sendTMDBRequest(
            'search/tv',
            [
                'query' => $query,
                'include_adult' => true,
                'language' => 'en-US',
                'page' => $page,
            ]
        );
    }

    private function getSeriesDetail(int $series_id)
    {
        return $this->sendTMDBRequest(
            "tv/{$series_id}",
            [
                'language' => 'en-US',
                'append_to_response' => 'content_ratings,external_ids',
            ]
        );
    }

    private function getSeriesCast(int $series_id)
    {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function getSeasonDetail(int $series_id, int $season_number)
    {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}",
            [
                'language' => 'en-US',
                'append_to_response' => 'external_ids',
            ]
        );
    }

    private function getSeasonCast(int $series_id, int $season_number)
    {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function getEpisodeDetail(int $series_id, int $season_number, int $episode_number)
    {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}/episode/{$episode_number}",
            [
                'language' => 'en-US',
                'append_to_response' => 'external_ids',
            ]
        );
    }

    private function getEpisodeCast(int $series_id, int $season_number, int $episode_number)
    {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}/episode/{$episode_number}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function sendTMDBRequest(string $endpoint, array $queryParams = [])
    {
        if (empty($endpoint)) {
            abort(400, 'Endpoint is required');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('tmdb.api.auth_key'),
            'accept' => 'application/json',
        ])->withQueryParameters($queryParams)->get(config('tmdb.api.base_url').'/'.config('tmdb.api.version').'/'.$endpoint);

        return $response->object();

        // search movies: https://api.themoviedb.org/3/search/movie { query, include_adult, language, primary_release_year, page, region, year }
        // search TV(seasons): https://api.themoviedb.org/3/search/tv { query, first_air_date_year, include_adult, language, page, year }
        // movie detail: https://api.themoviedb.org/3/movie/{movie_id} { append_to_response, language }
        // movie credits (cast): https://api.themoviedb.org/3/movie/{movie_id}/credits { language }
        // people (cast): https://api.themoviedb.org/3/person/{person_id} { append_to_response, language }
        // TV Series detail: https://api.themoviedb.org/3/tv/{series_id} { append_to_response, language }
        // TV Series credits (cast): https://api.themoviedb.org/3/tv/{series_id}/credits { language }
        // TV Seasons detail: https://api.themoviedb.org/3/tv/{series_id}/season/{season_number} { append_to_response, language }
        // TV Seasons credits (cast): https://api.themoviedb.org/3/tv/{series_id}/season/{season_number}/credits { language }
        // TV Episodes detail: https://api.themoviedb.org/3/tv/{series_id}/season/{season_number}/episode/{episode_number} { append_to_response, language }
        // TV Episodes credits (cast): https://api.themoviedb.org/3/tv/{series_id}/season/{season_number}/episode/{episode_number}/credits { language }
    }
}
