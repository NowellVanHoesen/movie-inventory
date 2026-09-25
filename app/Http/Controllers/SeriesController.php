<?php

namespace App\Http\Controllers;

use App\Jobs\processSeries;
use App\Models\Certification;
use App\Models\Genre;
use App\Models\Series;
use App\Traits\InteractsWithTMDB;
use App\Traits\MediaTypeHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class SeriesController extends Controller {
    use InteractsWithTMDB, MediaTypeHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index() {
        $query = Series::query();

        $genreNames = json_decode(request()->cookie('seriesSelectedGenres', '[]'), true) ?: [];

        // These cookies sit in bootstrap/app.php's encryptCookies except-list so the
        // filter component can write them from JS, which also makes them fully
        // client-writable. Treat them as untrusted: a JSON scalar such as "nope"
        // survives the ?: above and then throws inside whereIn, so require an actual
        // array, keep only string genre names, and cap how many we'll match on.
        $genreNames = is_array($genreNames)
            ? array_slice(array_values(array_filter($genreNames, 'is_string')), 0, 50)
            : [];

        if (! empty($genreNames)) {
            $query->whereHas('genres', function ($genreQuery) use ($genreNames) {
                $genreQuery->whereIn('name', $genreNames);
            });
        }

        $sortCol = request()->cookie('seriesSortCol', 'name_sortable');

        // Same reasoning: an unrecognized column would reach orderBy() and throw an
        // unhandled "Column not found" 500 that the user can't clear from the UI,
        // since the page they'd fix it on is the page that's failing.
        if (! in_array($sortCol, ['name_sortable', 'first_air_date', 'purchase_date'], true)) {
            $sortCol = 'name_sortable';
        }

        $sortDir = request()->cookie('seriesSortDir', 'asc');
        $secondarySort = 'name_sortable';

        if ($sortCol === 'name_sortable') {
            $secondarySort = 'first_air_date';
        }

        if ($sortDir === 'desc') {
            $query->orderByDesc($sortCol)->orderBy($secondarySort);
        } else {
            if ($sortCol === 'purchase_date') {
                $query->orderByRaw('purchase_date is null');
            }

            $query->orderBy($sortCol)->orderBy($secondarySort);
        }

        $series = $query->paginate(24);

        $page_title = config('app.name') . ' - Series List';

        return inertia('Series/Index', [
            'series' => Inertia::scroll(fn () => $series->toResourceCollection()),
            'page_title' => $page_title,
            // A closure so Inertia skips the query entirely on the `only: ['series']`
            // partial reloads that infinite scroll and the filter panel both issue.
            'genres' => fn () => Genre::has('series')->select('name')->orderBy('name')->get()->toResourceCollection(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        $data = [];

        if (! empty($request['query'])) {
            $attributes = $request->validate([
                'query' => ['min:2'],
            ]);

            // Escape LIKE wildcards, as SearchController does, so "100%" isn't match-all.
            $data['local_results'] = Series::where('name_normalized', 'like', '%' . addcslashes($attributes['query'], '%_\\') . '%')->get();

            $data['search_results'] = $this->searchSeries($attributes['query']);

            $data['search_term'] = $attributes['query'];
        } elseif (! empty($request['series_id'])) {
            $attributes = $request->validate([
                'series_id' => ['integer'],
                'search_term' => ['min:2'],
            ]);

            $series_detail = $this->getSeriesDetail($attributes['series_id']);

            if ($series_detail === null) {
                return back()->withErrors(['series_id' => 'Could not load that series from TMDB. Please try again.']);
            }

            $genres = [];

            foreach ($series_detail->genres as $genre) {
                $genres[] = $genre->name;
            }

            $series_detail->genres = $genres;

            $data['media_types'] = $this->get_media_types();

            $data['series_detail'] = $series_detail;

            $data['search_term'] = $attributes['search_term'] ?? '';
        }

        $data['page_title'] = config('app.name') . ' - Add Series';

        return Inertia::render('Series/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $attributes = $request->validate([
            'series_id' => ['required', 'integer'],
            // Required, unlike movies: series have no wishlist, so every series is
            // owned and must carry a purchase date. Previously this was `nullable`,
            // and a null got as far as processSeries' typed property and 500'd with
            // a TypeError instead of returning a validation message.
            'purchase_date' => ['required', 'date_format:Y-m-d'],
            'media_type' => ['array'],
            // Matches the rule update() already uses: without it, an unknown id
            // reaches attach() and fails on the pivot's foreign key instead.
            'media_type.*' => ['integer', 'exists:media_types,id'],
            'season_numbers' => ['array'],
        ]);

        $series_detail = $this->getSeriesDetail($attributes['series_id']);

        if ($series_detail === null) {
            return back()->withErrors(['series_id' => 'Could not load that series from TMDB. Nothing was saved — please try again.']);
        }

        $certification_name = 'NR';

        foreach ($series_detail->content_ratings->results as $rDate) {
            if ($rDate->iso_3166_1 !== 'US') {
                continue;
            }

            $certification_name = $rDate->rating;

            break;
        }

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
            'certification_id' => Certification::idFor($certification_name),
            'first_air_date' => $series_detail->first_air_date,
            'purchase_date' => $attributes['purchase_date'],
        ]);

        // syncWithoutDetaching over a per-row attach(): one insert instead of N, and
        // it won't trip the pivot's composite primary key if a row already exists.
        $series->genres()->syncWithoutDetaching(
            collect($series_detail->genres)->pluck('id')->all()
        );

        if (! empty($attributes['media_type'])) {
            $series->media_types()->syncWithoutDetaching($attributes['media_type']);
        }

        processSeries::dispatch([
            'series_id' => $series->id,
            'media_type' => $attributes['media_type'] ?? [],
            'purchase_date' => $attributes['purchase_date'],
        ]);

        return redirect()->route('series.show', $series);
    }

    /**
     * Display the specified resource.
     */
    public function show(Series $series) {
        $series->media_types_display = $this->get_media_types_display($series->media_types);

        // $recs = $this->getSeriesRecommendations($series->id);

        // $owned_recs = Series::whereIn( 'id', Arr::pluck( $recs, 'id' ) )->get();

        $series->load('genres', 'cast_members', 'seasons');

        $page_title = config('app.name') . ' - Series: ' . $series->name;

        return inertia('Series/Show', [
            'series' => $series,
            'page_title' => $page_title,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Series $series) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Series $series) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Series $series) {
        //
    }
}
