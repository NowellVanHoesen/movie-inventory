<?php

use App\Models\CastMember;
use App\Models\Episode;
use Illuminate\Support\Facades\DB;
use Tests\Browser;

/**
 * Episode cast isn't in Series/Show's initial payload; selecting an episode fetches
 * it with an `only: ['episode_cast']` partial reload. Pick a guest credited on the
 * episode alone (not on the series or season), so the only way their name can reach
 * the cast panel is through that reload.
 */
it('loads an episode\'s guest cast into the cast panel when the episode is selected', function () {
    $row = DB::table('cast_member_episode as ce')
        ->join('episodes as e', 'e.id', '=', 'ce.episode_id')
        ->join('seasons as s', 's.id', '=', 'e.season_id')
        ->whereNotNull('ce.character')
        ->where('ce.character', 'not like', '%uncredited%')
        ->whereNotExists(fn ($q) => $q->from('cast_member_series as cs')
            ->whereColumn('cs.cast_member_id', 'ce.cast_member_id')
            ->whereColumn('cs.series_id', 's.series_id'))
        ->whereNotExists(fn ($q) => $q->from('cast_member_season as css')
            ->whereColumn('css.cast_member_id', 'ce.cast_member_id')
            ->whereColumn('css.season_id', 's.id'))
        ->select('ce.cast_member_id', 'ce.episode_id')
        ->first();

    $guest = CastMember::findOrFail($row->cast_member_id);
    $episode = Episode::with('season.series')->findOrFail($row->episode_id);
    $season = $episode->season;
    $series = $season->series;

    $this->browse(function (Browser $browser) use ($series, $season, $episode, $guest) {
        $hasGuest = fn (CastMember $member) => sprintf(
            "[...document.querySelectorAll('li a')].some(a => a.textContent.trim() === %s)",
            json_encode($member->name)
        );

        $browser
            ->visit(route('series.show', $series))
            ->waitFor("@season-btn-{$season->id}")
            ->click("@season-btn-{$season->id}")
            ->waitFor("@episode-btn-{$episode->id}");

        expect($browser->script("return {$hasGuest($guest)};")[0])->toBeFalse();

        $browser
            ->click("@episode-btn-{$episode->id}")
            ->waitForText('Runtime');

        // The panel shows 20 names until expanded, and episode guests sort last.
        $browser->script("[...document.querySelectorAll('button')].find(b => b.textContent.trim() === 'Show All')?.click();");

        $browser->waitUntil($hasGuest($guest));

        // preserveUrl: selecting an episode must not leave ?episode= in the address bar.
        expect($browser->driver->getCurrentURL())->toBe(route('series.show', $series));
    });
});
