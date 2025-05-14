<?php

namespace App\Http\Controllers;

use App\Jobs\processSeries;
use App\Models\Certification;
use App\Models\Episode;
use App\Models\MediaType;
use App\Models\Season;
use App\Models\Series;
use App\Traits\InteractsWithTMDB;
use App\Traits\MediaTypeHelpers;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    use InteractsWithTMDB, MediaTypeHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('series.index', ['series' => Series::orderBy('name', 'asc')->simplePaginate(24)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = [];

        if (! empty($request['query'])) {
            $attributes = $request->validate([
                'query' => ['min:2'],
            ]);

            $data['local_results'] = Series::search($attributes['query'])->get();

            $data['search_results'] = $this->searchSeries($attributes['query']);

            $data['search_term'] = $attributes['query'];
        } elseif (! empty($request['series_id'])) {
            $attributes = $request->validate([
                'series_id' => ['integer'],
                'search_term' => ['min:2'],
            ]);

            $series_detail = $this->getSeriesDetail($attributes['series_id']);

            $genres = [];

            foreach ($series_detail->genres as $genre) {
                $genres[] = $genre->name;
            }

            $series_detail->genres = $genres;

            $data['media_types'] = $this->get_media_types();

            $data['series_detail'] = $series_detail;

            $data['search_term'] = $attributes['search_term'];
        }

        return view('series.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'series_id' => ['integer'],
            'purchase_date' => ['nullable', 'date_format:Y-m-d'],
            'media_type' => ['array'],
            'season_numbers' => ['array'],
        ]);

        $series_detail = $this->getSeriesDetail($attributes['series_id']);

        $certification_name = 'NR';

        foreach ($series_detail->content_ratings->results as $rDate) {
            if ($rDate->iso_3166_1 !== 'US') {
                continue;
            }

            $certification_name = $rDate->rating;
        }

        $certification_id = Certification::select('id')->where('name', '=', $certification_name)->first();

        $series = Series::create([
            'id' => $series_detail->id,
            'imdb_id' => $series_detail->external_ids->imdb_id ?: null,
            'name' => $series_detail->name,
            'original_name' => $series_detail->original_name,
            'tagline' => $series_detail->tagline,
            'overview' => $series_detail->overview,
            'homepage' => $series_detail->homepage,
            'poster_path' => $series_detail->poster_path ?: null,
            'backdrop_path' => $series_detail->backdrop_path ?: null,
            'certification_id' => $certification_id->id,
            'first_air_date' => $series_detail->first_air_date,
            'purchase_date' => $attributes['purchase_date'],
        ]);

        // add genres
        foreach ($series_detail->genres as $genre) {
            $series->genres()->attach($genre->id);
        }

        if (! empty($attributes['media_type'])) {
            foreach ($attributes['media_type'] as $media_type_id) {
                $series->media_types()->attach($media_type_id);
            }
        }

        processSeries::dispatch([
            'series_id' => $series->id,
            'media_type' => $attributes['media_type'] ?: [],
            'season_numbers' => $attributes['season_numbers'] ?: [],
            'purchase_date' => $attributes['purchase_date'],
        ]);

        return redirect('/series/'.$series->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Series $series)
    {
        $series->media_types_display = $this->get_media_types_display($series->media_types);

        return view('series.show', ['series' => $series]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Series $series)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Series $series)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Series $series)
    {
        //
    }

    public function showSeason(Series $series, Season $season)
    {
        $series->media_types_display = $this->get_media_types_display($series->media_types);

        return view('series.seasons.show', ['series' => $series, 'season' => $season]);
    }

    public function showEpisode(Series $series, Season $season, Episode $episode)
    {
        $series->media_types_display = $this->get_media_types_display($series->media_types);

        return view('series.seasons.episodes.show', ['series' => $series, 'season' => $season, 'episode' => $episode]);
    }
}
