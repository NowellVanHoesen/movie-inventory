<?php

use App\Models\CastMember;
use App\Models\Season;
use App\Models\Series;
use Illuminate\Support\Facades\DB;
use Tests\Browser;

/**
 * Series/Show owns the selected season/episode and swaps SeasonList <-> SeasonInfo and
 * EpisodeList <-> EpisodeInfo around it. These walk each of those swaps in both
 * directions, read-only, against whichever dataset series fits.
 */

/**
 * A series plus two of its seasons, each with at least two episodes. The two seasons
 * differ in episode count where the dataset allows, so showing the wrong season's
 * episodes can't pass a count check by coincidence.
 *
 * @return array{0: Series, 1: Season, 2: Season}
 */
function seriesShowFixture(): array {
    $candidates = Series::with(['seasons' => fn ($query) => $query->withCount('episodes')])->orderBy('id')->get();

    $fallback = null;

    foreach ($candidates as $series) {
        $seasons = $series->seasons->where('episodes_count', '>=', 2)->values();

        if ($seasons->count() < 2) {
            continue;
        }

        $first = $seasons->first();
        $second = $seasons->firstWhere('episodes_count', '!=', $first->episodes_count);

        if ($second) {
            return [$series, $first, $second];
        }

        $fallback ??= [$series, $first, $seasons->get(1)];
    }

    return $fallback ?? throw new RuntimeException('No series in the Dusk dataset has two seasons with two or more episodes.');
}

function countDuskPrefix(Browser $browser, string $prefix): int {
    return $browser->script("return document.querySelectorAll('[dusk^={$prefix}]').length;")[0];
}

function hasHeading(Browser $browser, string $tag, string $text): bool {
    return $browser->script(sprintf(
        "return [...document.querySelectorAll('%s')].some(h => h.textContent.trim() === %s);",
        $tag,
        json_encode($text)
    ))[0];
}

it('shows the series detail and its season list, with nothing selected', function () {
    [$series] = seriesShowFixture();
    $series->load('genres');

    $this->browse(function (Browser $browser) use ($series) {
        $browser
            ->visit(route('series.show', $series))
            ->waitFor('@series-info')
            ->assertSeeIn('@series-info', $series->name)
            ->assertSeeIn('@series-info', "({$series->certification->name})")
            ->assertSeeIn('@series-info', "First Air Date: {$series->first_air_date}");

        foreach ($series->genres as $genre) {
            $browser->assertSeeIn('@series-info', $genre->name);
        }

        expect(hasHeading($browser, 'h2', 'Seasons'))->toBeTrue()
            ->and(countDuskPrefix($browser, 'season-btn-'))->toBe($series->seasons->count())
            ->and(hasHeading($browser, 'h3', 'Episodes'))->toBeFalse()
            ->and(countDuskPrefix($browser, 'episode-btn-'))->toBe(0);

        $browser
            ->assertMissing('@season-info')
            ->assertMissing('@episode-info');
    });
});

it('swaps the season list for the selected season\'s detail and episode list', function () {
    [$series, $season] = seriesShowFixture();

    $this->browse(function (Browser $browser) use ($series, $season) {
        $browser
            ->visit(route('series.show', $series))
            ->waitFor("@season-btn-{$season->id}")
            ->clickViaJs("@season-btn-{$season->id}")
            ->waitFor('@season-info')
            ->assertSeeIn('@season-info', $season->name)
            ->assertSeeIn('@season-info', "Episodes: {$season->episodes_count}")
            ->assertMissing('@episode-info');

        expect(hasHeading($browser, 'h2', 'Seasons'))->toBeFalse()
            ->and(countDuskPrefix($browser, 'season-btn-'))->toBe(0)
            ->and(hasHeading($browser, 'h3', 'Episodes'))->toBeTrue()
            ->and(countDuskPrefix($browser, 'episode-btn-'))->toBe($season->episodes_count);
    });
});

it('returns to the season list when the season is closed', function () {
    [$series, $season] = seriesShowFixture();

    $this->browse(function (Browser $browser) use ($series, $season) {
        $browser
            ->visit(route('series.show', $series))
            ->waitFor("@season-btn-{$season->id}")
            ->clickViaJs("@season-btn-{$season->id}")
            ->waitFor('@season-close-btn')
            ->clickViaJs('@season-close-btn')
            ->waitFor("@season-btn-{$season->id}")
            ->assertMissing('@season-info');

        expect(hasHeading($browser, 'h2', 'Seasons'))->toBeTrue()
            ->and(countDuskPrefix($browser, 'season-btn-'))->toBe($series->seasons->count())
            ->and(hasHeading($browser, 'h3', 'Episodes'))->toBeFalse()
            ->and(countDuskPrefix($browser, 'episode-btn-'))->toBe(0);
    });
});

it('swaps the episode list for the selected episode\'s detail, keeping the season open', function () {
    [$series, $season] = seriesShowFixture();
    $episode = $season->episodes()->where('overview', '!=', '')->firstOrFail();

    $this->browse(function (Browser $browser) use ($series, $season, $episode) {
        $browser
            ->visit(route('series.show', $series))
            ->waitFor("@season-btn-{$season->id}")
            ->clickViaJs("@season-btn-{$season->id}")
            ->waitFor("@episode-btn-{$episode->id}")
            ->clickViaJs("@episode-btn-{$episode->id}")
            ->waitFor('@episode-info')
            ->assertSeeIn('@episode-info', $episode->name)
            ->assertSeeIn('@episode-info', "Runtime: {$episode->runtime} min.")
            ->assertSeeIn('@season-info', $season->name);

        // Compared whitespace-insensitively: TMDB overviews carry stray line breaks
        // and double spaces that the browser collapses.
        $squash = fn (string $text) => preg_replace('/\s+/u', '', $text);
        $rendered = $browser->script("return document.querySelector('[dusk=episode-info]').textContent;")[0];

        expect($squash($rendered))->toContain($squash($episode->overview))
            ->and(hasHeading($browser, 'h3', 'Episodes'))->toBeFalse()
            ->and(countDuskPrefix($browser, 'episode-btn-'))->toBe(0);
    });
});

it('returns to the episode list when the episode is closed, keeping the season open', function () {
    [$series, $season] = seriesShowFixture();
    $episode = $season->episodes()->firstOrFail();

    $this->browse(function (Browser $browser) use ($series, $season, $episode) {
        $browser
            ->visit(route('series.show', $series))
            ->waitFor("@season-btn-{$season->id}")
            ->clickViaJs("@season-btn-{$season->id}")
            ->waitFor("@episode-btn-{$episode->id}")
            ->clickViaJs("@episode-btn-{$episode->id}")
            ->waitFor('@episode-close-btn')
            ->clickViaJs('@episode-close-btn')
            ->waitFor("@episode-btn-{$episode->id}")
            ->assertMissing('@episode-info')
            ->assertSeeIn('@season-info', $season->name);

        expect(hasHeading($browser, 'h3', 'Episodes'))->toBeTrue()
            ->and(countDuskPrefix($browser, 'episode-btn-'))->toBe($season->episodes_count)
            ->and(countDuskPrefix($browser, 'season-btn-'))->toBe(0);
    });
});

it('clears the selected episode when its season is closed, so the next season opens on its episode list', function () {
    [$series, $season, $otherSeason] = seriesShowFixture();
    $episode = $season->episodes()->firstOrFail();

    $this->browse(function (Browser $browser) use ($series, $season, $otherSeason, $episode) {
        $browser
            ->visit(route('series.show', $series))
            ->waitFor("@season-btn-{$season->id}")
            ->clickViaJs("@season-btn-{$season->id}")
            ->waitFor("@episode-btn-{$episode->id}")
            ->clickViaJs("@episode-btn-{$episode->id}")
            ->waitFor('@episode-info')
            ->clickViaJs('@season-close-btn')
            ->waitFor("@season-btn-{$otherSeason->id}")
            ->assertMissing('@season-info')
            ->assertMissing('@episode-info')
            ->clickViaJs("@season-btn-{$otherSeason->id}")
            ->waitFor('@season-info')
            ->assertSeeIn('@season-info', $otherSeason->name)
            ->assertMissing('@episode-info');

        expect(hasHeading($browser, 'h3', 'Episodes'))->toBeTrue()
            ->and(countDuskPrefix($browser, 'episode-btn-'))->toBe($otherSeason->episodes_count)
            ->and($browser->script("return document.querySelector('[dusk=episode-btn-{$episode->id}]') !== null;")[0])->toBeFalse();
    });
});

/**
 * Season cast is in the initial payload but only merged into the panel while that
 * season is selected. Pick someone credited on a season and not on the series, so
 * their name can only be there because of the selection.
 */
it('adds a season\'s cast to the cast panel while the season is selected, and removes it on close', function () {
    $row = DB::table('cast_member_season as cs')
        ->join('seasons as s', 's.id', '=', 'cs.season_id')
        ->whereNotNull('cs.character')
        ->where('cs.character', 'not like', '%uncredited%')
        ->whereNotExists(fn ($q) => $q->from('cast_member_series as cse')
            ->whereColumn('cse.cast_member_id', 'cs.cast_member_id')
            ->whereColumn('cse.series_id', 's.series_id'))
        ->select('cs.cast_member_id', 'cs.season_id')
        ->first();

    $member = CastMember::findOrFail($row->cast_member_id);
    $season = Season::with('series')->findOrFail($row->season_id);

    $this->browse(function (Browser $browser) use ($season, $member) {
        $hasMember = sprintf(
            "[...document.querySelectorAll('li a')].some(a => a.textContent.trim() === %s)",
            json_encode($member->name)
        );

        $browser
            ->visit(route('series.show', $season->series))
            ->waitFor("@season-btn-{$season->id}");

        expect($browser->script("return {$hasMember};")[0])->toBeFalse();

        $browser
            ->clickViaJs("@season-btn-{$season->id}")
            ->waitFor('@season-info');

        // The panel shows 20 names until expanded, and season cast sorts after series cast.
        $browser->script("document.querySelector('[dusk=cast-show-all-btn]')?.click();");

        $browser
            ->waitUntil($hasMember)
            ->clickViaJs('@season-close-btn')
            ->waitUntil("!({$hasMember})");
    });
});
