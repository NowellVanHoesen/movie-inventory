<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

trait InteractsWithTMDB {
    private function getMovieDetail(int $movie_id) {
        return $this->sendTMDBRequest(
            "movie/{$movie_id}",
            [
                'language' => 'en-US',
                'append_to_response' => 'release_dates',
            ]
        );
    }

    private function getMovieCollection(int $collection_id) {
        return $this->sendTMDBRequest(
            "collection/{$collection_id}",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function getMovieCast(int $movie_id) {
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

    // getMovieRecommendations()/getSeriesRecommendations() were removed: both began
    // with an unconditional `return collect([])`, so ~40 lines of paginated fetching
    // were unreachable, and their only callers in MoviesController/SeriesController
    // `show()` are commented out. Recover from git history to re-enable.

    private function searchMovies(string $query, ?string $year) {
        $args = [
            'query' => $query,
            'language' => 'en-US',
            'page' => 1,
        ];

        if ($year) {
            $args['year'] = $year;
        }

        return $this->sendTMDBRequest(
            'search/movie',
            $args
        );
    }

    private function searchSeries(string $query, int $page = 1) {
        return $this->sendTMDBRequest(
            'search/tv',
            [
                'query' => $query,
                'language' => 'en-US',
                'page' => $page,
            ]
        );
    }

    private function getSeriesDetail(int $series_id) {
        return $this->sendTMDBRequest(
            "tv/{$series_id}",
            [
                'language' => 'en-US',
                'append_to_response' => 'content_ratings,external_ids',
            ]
        );
    }

    private function getSeriesCast(int $series_id) {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function getSeasonDetail(int $series_id, int $season_number) {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}",
            [
                'language' => 'en-US',
                'append_to_response' => 'external_ids',
            ]
        );
    }

    private function getSeasonCast(int $series_id, int $season_number) {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    private function getEpisodeDetail(int $series_id, int $season_number, int $episode_number) {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}/episode/{$episode_number}",
            [
                'language' => 'en-US',
                'append_to_response' => 'external_ids',
            ]
        );
    }

    private function getEpisodeCast(int $series_id, int $season_number, int $episode_number) {
        return $this->sendTMDBRequest(
            "tv/{$series_id}/season/{$season_number}/episode/{$episode_number}/credits",
            [
                'language' => 'en-US',
            ]
        );
    }

    /**
     * Assert a TMDB response arrived, for callers that cannot meaningfully continue
     * without it (queued jobs). Throwing marks the job failed with a readable reason
     * instead of letting a null propagate into "Attempt to read property on null".
     */
    private function requireTMDBResponse($response, string $context) {
        if ($response === null) {
            throw new RuntimeException("TMDB request failed for {$context}. See the preceding TMDB error log entry.");
        }

        return $response;
    }

    /**
     * Returns the decoded response object, or null if TMDB refused the request.
     *
     * Callers MUST treat null as a real possibility — TMDB rate-limits, has outages,
     * and 404s ids that have been removed upstream. Dereferencing the return value
     * without checking turns any of those into "Attempt to read property on null".
     */
    private function sendTMDBRequest(string $endpoint, array $queryParams = []) {
        if (empty($endpoint)) {
            throw new InvalidArgumentException('TMDB endpoint is required');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('tmdb.api.auth_key'),
            'accept' => 'application/json',
        ])
            ->connectTimeout(5)
            ->timeout(15)
            ->withQueryParameters($queryParams)
            ->get(config('tmdb.api.base_url') . '/' . config('tmdb.api.version') . '/' . $endpoint);

        if ($response->successful()) {
            return $response->object();
        }

        // Previously this returned null with no trace at all, so a rate-limit or
        // outage surfaced only as a null-dereference 500 with nothing in the log.
        Log::error('TMDB request failed', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'body' => substr($response->body(), 0, 500),
        ]);

        return null;

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
