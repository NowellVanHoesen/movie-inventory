<?php

use App\Models\Movie;
use App\Models\User;
use Tests\Browser;

/**
 * Every test starts by clearing the component's cookies (selectedGenres, sortCol,
 * sortDir) on a loaded page, then reloading, so state left behind by a previous
 * test in this shared browser session can't leak in and make results order-dependent.
 */
function clearMovieFilterCookies(Browser $browser): void
{
    $browser->script([
        "document.cookie = 'selectedGenres=; path=/; max-age=0';",
        "document.cookie = 'sortCol=; path=/; max-age=0';",
        "document.cookie = 'sortDir=; path=/; max-age=0';",
    ]);
}

/**
 * The filter panel is absolutely positioned over the top of the page, where it's
 * overlapped by the site header (avatar/nav) and, locally, Laravel Debugbar. A
 * native WebDriver click on its buttons/inputs lands on whichever of those sits on
 * top instead, so every interaction inside the panel goes through JS (clickViaJs),
 * which dispatches the click directly on the target element regardless of what
 * visually overlaps it. This also lets us hit checkboxes/radios that are
 * themselves visually hidden (styled via their `<label>`), which a native click
 * can't reach at all.
 */
it('opens and closes the filter panel', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->assertMissing('@filter-panel')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertVisible('@filter-panel')
            ->clickViaJs('@filter-close-btn')
            ->waitUntilMissing('@filter-panel');
    });
});

it('defaults to sorting by release date descending when no preferences are stored', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertChecked('@sort-col-release_date')
            ->assertChecked('@sort-dir-desc')
            ->assertNotChecked('@sort-col-title_sortable')
            ->assertNotChecked('@sort-col-purchase_date')
            ->assertNotChecked('@sort-dir-asc');
    });
});

it('disables the Clear Genre Filter button until a genre is selected', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertDisabled('@clear-genre-filter-btn')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->assertChecked('@genre-checkbox-Documentary')
            ->assertEnabled('@clear-genre-filter-btn')
            ->clickViaJs('@clear-genre-filter-btn')
            ->assertNotChecked('@genre-checkbox-Documentary')
            ->assertDisabled('@clear-genre-filter-btn');
    });
});

it('filters the movie list to only movies in the selected genre', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))
            ->orderByDesc('release_date')
            ->orderBy('title_sortable')
            ->pluck('slug')
            ->all();
        $unrelatedMovie = Movie::whereDoesntHave('genres', fn ($q) => $q->where('name', 'Documentary'))->firstOrFail();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.count($expectedSlugs))
            ->assertMissing("@movie-btn-{$unrelatedMovie->slug}");

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=movie-btn-]')).map(el => el.getAttribute('dusk').replace('movie-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('filters by multiple genres using OR logic', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Movie::whereHas('genres', fn ($q) => $q->whereIn('name', ['Documentary', 'Western']))
            ->orderByDesc('release_date')
            ->orderBy('title_sortable')
            ->pluck('slug')
            ->all();
        $unrelatedMovie = Movie::whereDoesntHave('genres', fn ($q) => $q->whereIn('name', ['Documentary', 'Western']))->firstOrFail();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->clickViaJs('@genre-checkbox-Western')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            // Straddles two pages (28 matches, 24 per page), so InfiniteScroll has to
            // auto-follow up with a second fetch — allow more time than a single
            // network round trip needs.
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.count($expectedSlugs), 10)
            ->assertMissing("@movie-btn-{$unrelatedMovie->slug}");

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=movie-btn-]')).map(el => el.getAttribute('dusk').replace('movie-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('restores the full movie list after clearing a genre filter, and remembers the prior selection when the panel is reopened', function () {
    $this->browse(function (Browser $browser) {
        $documentaryCount = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))->count();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.$documentaryCount)
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->assertChecked('@genre-checkbox-Documentary')
            ->clickViaJs('@clear-genre-filter-btn')
            ->assertNotChecked('@genre-checkbox-Documentary')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === 24');
    });
});

it('sorts the filtered list by title ascending when selected', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))
            ->orderBy('title_sortable')
            ->orderBy('release_date')
            ->pluck('slug')
            ->all();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->clickViaJs('@sort-col-title_sortable')
            ->clickViaJs('@sort-dir-asc')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.count($expectedSlugs));

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=movie-btn-]')).map(el => el.getAttribute('dusk').replace('movie-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('sorts the filtered list by release date ascending when selected', function () {
    $this->browse(function (Browser $browser) {
        $expectedSlugs = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))
            ->orderBy('release_date')
            ->orderBy('title_sortable')
            ->pluck('slug')
            ->all();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->clickViaJs('@sort-col-release_date')
            ->clickViaJs('@sort-dir-asc')
            ->clickViaJs('@apply-filters-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.count($expectedSlugs));

        $displayedSlugs = $browser->script(
            "return Array.from(document.querySelectorAll('[dusk^=movie-btn-]')).map(el => el.getAttribute('dusk').replace('movie-btn-', ''));"
        )[0];

        expect($displayedSlugs)->toBe($expectedSlugs);
    });
});

it('always places movies without a purchase date last when sorting by purchase date, regardless of direction', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::factory()->create());

        $totalDocumentary = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))->count();
        $withoutPurchaseSlugs = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))
            ->whereNull('purchase_date')
            ->pluck('slug')
            ->sort()
            ->values()
            ->all();
        $withPurchaseCount = $totalDocumentary - count($withoutPurchaseSlugs);

        expect($withPurchaseCount)->toBeGreaterThan(0);
        expect($withoutPurchaseSlugs)->not->toBeEmpty();

        foreach (['asc', 'desc'] as $direction) {
            $browser->visit(route('movies.index'));

            clearMovieFilterCookies($browser);

            $browser
                ->visit(route('movies.index'))
                ->waitFor('@filter-toggle-btn')
                ->clickViaJs('@filter-toggle-btn')
                ->waitFor('@filter-panel')
                ->clickViaJs('@genre-checkbox-Documentary')
                ->clickViaJs('@sort-col-purchase_date')
                ->clickViaJs("@sort-dir-{$direction}")
                ->clickViaJs('@apply-filters-btn')
                ->waitUntilMissing('@filter-panel')
                ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.$totalDocumentary);

            $slugs = $browser->script(
                "return Array.from(document.querySelectorAll('[dusk^=movie-btn-]')).map(el => el.getAttribute('dusk').replace('movie-btn-', ''));"
            )[0];

            $trailingSlugs = collect(array_slice($slugs, $withPurchaseCount))->sort()->values()->all();

            expect($trailingSlugs)->toBe($withoutPurchaseSlugs);
        }
    });
});

it('persists a genre selection to the cookie immediately, even if the panel is closed without clicking Apply', function () {
    $this->browse(function (Browser $browser) {
        $expectedCount = Movie::whereHas('genres', fn ($q) => $q->where('name', 'Documentary'))->count();

        $browser
            ->loginAs(User::factory()->create())
            ->visit(route('movies.index'));

        clearMovieFilterCookies($browser);

        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->clickViaJs('@filter-toggle-btn')
            ->waitFor('@filter-panel')
            ->clickViaJs('@genre-checkbox-Documentary')
            ->clickViaJs('@filter-close-btn')
            ->waitUntilMissing('@filter-panel')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === 24');

        // Nothing was applied, so the on-screen list is still unfiltered. A fresh
        // page load, however, has the server honor the cookie written the moment
        // the checkbox was toggled.
        $browser
            ->visit(route('movies.index'))
            ->waitFor('@filter-toggle-btn')
            ->waitUntil('document.querySelectorAll(\'[dusk^=movie-btn-]\').length === '.$expectedCount);

        $finalCount = $browser->script("return document.querySelectorAll('[dusk^=movie-btn-]').length;")[0];

        expect($finalCount)->toBe($expectedCount);
    });
});
