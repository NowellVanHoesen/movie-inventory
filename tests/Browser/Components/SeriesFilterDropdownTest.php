<?php

use App\Models\Series;
use App\Models\User;
use Tests\Browser;

/**
 * Every test starts by clearing the component's cookies (seriesSelectedGenres,
 * seriesSortCol, seriesSortDir) on a loaded page, then reloading, so state left
 * behind by a previous test in this shared browser session can't leak in and make
 * results order-dependent.
 */
function clearSeriesFilterCookies(Browser $browser): void
{
    $browser->script([
        "document.cookie = 'seriesSelectedGenres=; path=/; max-age=0';",
        "document.cookie = 'seriesSortCol=; path=/; max-age=0';",
        "document.cookie = 'seriesSortDir=; path=/; max-age=0';",
    ]);
}

/**
 * Unlike movies, every series in the real dump has a purchase_date set (there's no
 * "wishlist" series in this collection), so there's no data here to exercise the
 * purchase-date-nulls-sort-last edge case the way MovieFilterDropdownTest.php does.
 * That sort logic is identical/shared between the two controllers and is already
 * covered there, so it's intentionally not duplicated here without real data to
 * back it up.
 */
it('opens and closes the filter panel', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->assertMissing('@filter-panel')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertVisible('@filter-panel')
            ->clickViaJs('@filter-close-btn')
            ->waitUntilMissing('@filter-panel');
    });
});

it('defaults to sorting by name ascending when no preferences are stored', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertChecked('@sort-col-name_sortable')
            ->assertChecked('@sort-dir-asc')
            ->assertNotChecked('@sort-col-first_air_date')
            ->assertNotChecked('@sort-col-purchase_date')
            ->assertNotChecked('@sort-dir-desc');
    });
});

it('disables the Clear Genre Filter button until a genre is selected', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertDisabled('@clear-genre-filter-btn')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->assertChecked('@genre-checkbox-Mystery')
            ->assertEnabled('@clear-genre-filter-btn')
            ->clickViaJs('@clear-genre-filter-btn')
            ->assertNotChecked('@genre-checkbox-Mystery')
            ->assertDisabled('@clear-genre-filter-btn');
    });
});

it('filters the series list to only series in the selected genre', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Series::whereHas('genres', fn ($q) => $q->where('name', 'Mystery'))
            ->orderBy('name_sortable')
            ->orderBy('first_air_date')
            ->pluck('slug')
            ->all();
        $unrelatedSeries = Series::whereDoesntHave('genres', fn ($q) => $q->where('name', 'Mystery'))->firstOrFail();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === '.count($expectedSlugs))
            ->assertMissing("@series-btn-{$unrelatedSeries->slug}");

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=series-btn-]')).map(el => el.getAttribute('dusk').replace('series-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('filters by multiple genres using OR logic', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Series::whereHas('genres', fn ($q) => $q->whereIn('name', ['Mystery', 'Family']))
            ->orderBy('name_sortable')
            ->orderBy('first_air_date')
            ->pluck('slug')
            ->all();
        $unrelatedSeries = Series::whereDoesntHave('genres', fn ($q) => $q->whereIn('name', ['Mystery', 'Family']))->firstOrFail();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->clickViaJs('@genre-checkbox-Family')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel');

        // 27 matches, 24 per page. Series/Index.vue's InfiniteScroll uses a much
        // smaller buffer than the movies page, so the second page isn't prefetched
        // automatically on load here — an explicit scroll is needed to bring the
        // sentinel within range and trigger the follow-up fetch.
        $browser->script('window.scrollTo(0, document.body.scrollHeight);');

        $browser
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === '.count($expectedSlugs), 10)
            ->assertMissing("@series-btn-{$unrelatedSeries->slug}");

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=series-btn-]')).map(el => el.getAttribute('dusk').replace('series-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('restores the full series list after clearing a genre filter, and remembers the prior selection when the panel is reopened', function () {
    $this->browse(function (Browser $browser) {
        $mysteryCount = Series::whereHas('genres', fn ($q) => $q->where('name', 'Mystery'))->count();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === '.$mysteryCount)
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertChecked('@genre-checkbox-Mystery')
            ->clickViaJs('@clear-genre-filter-btn')
            ->assertNotChecked('@genre-checkbox-Mystery')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === 24');
    });
});

it('sorts the filtered list by first air date descending when selected', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Series::whereHas('genres', fn ($q) => $q->where('name', 'Mystery'))
            ->orderByDesc('first_air_date')
            ->orderBy('name_sortable')
            ->pluck('slug')
            ->all();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->clickViaJs('@sort-col-first_air_date')
            ->clickViaJs('@sort-dir-desc')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === '.count($expectedSlugs));

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=series-btn-]')).map(el => el.getAttribute('dusk').replace('series-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('sorts the filtered list by name descending when selected', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Series::whereHas('genres', fn ($q) => $q->where('name', 'Mystery'))
            ->orderByDesc('name_sortable')
            ->orderBy('first_air_date')
            ->pluck('slug')
            ->all();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->clickViaJs('@sort-col-name_sortable')
            ->clickViaJs('@sort-dir-desc')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === '.count($expectedSlugs));

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=series-btn-]')).map(el => el.getAttribute('dusk').replace('series-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('persists a genre selection to the cookie immediately, even if the panel is closed without clicking Apply', function () {
    $this->browse(function (Browser $browser) {
        $expectedCount = Series::whereHas('genres', fn ($q) => $q->where('name', 'Mystery'))->count();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('series.index'));

        clearSeriesFilterCookies($browser);

        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Mystery')
            ->clickViaJs('@filter-close-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === 24');

        // Nothing was applied, so the on-screen list is still unfiltered. A fresh
        // page load, however, has the server honor the cookie written the moment
        // the checkbox was toggled.
        $browser
            ->visit(route('series.index'))
            ->waitFor('@filter-toggle-btn')
            ->waitUntil('document.querySelectorAll(\'[dusk^=series-btn-]\').length === '.$expectedCount);

        $finalCount = $browser->script("return document.querySelectorAll('[dusk^=series-btn-]').length;")[0];

        expect($finalCount)->toBe($expectedCount);
    });
});
