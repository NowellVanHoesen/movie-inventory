<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppliesIndexPreferences {
    /**
     * Apply the genre filter and sort order that FilterDropdown.vue persists to cookies.
     *
     * Sortable columns are the title, the date, and purchase_date; whichever of the
     * title/date pair isn't the primary sort becomes the tiebreaker.
     *
     * @param  array{genres: string, sortCol: string, sortDir: string}  $cookieNames  the same names the page passes to FilterDropdown.vue's `cookieNames` prop
     */
    private function applyIndexPreferences(
        Builder $query,
        array $cookieNames,
        string $titleCol,
        string $dateCol,
        string $defaultSortCol,
        string $defaultSortDir,
    ): Builder {
        $genreNames = json_decode(request()->cookie($cookieNames['genres'], '[]'), true) ?: [];

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

        $sortCol = request()->cookie($cookieNames['sortCol'], $defaultSortCol);

        // Same reasoning: an unrecognized column would reach orderBy() and throw an
        // unhandled "Column not found" 500 that the user can't clear from the UI,
        // since the page they'd fix it on is the page that's failing.
        if (! in_array($sortCol, [$titleCol, $dateCol, 'purchase_date'], true)) {
            $sortCol = $defaultSortCol;
        }

        $sortDir = request()->cookie($cookieNames['sortDir'], $defaultSortDir);
        $secondarySort = $sortCol === $titleCol ? $dateCol : $titleCol;

        if ($sortDir === 'desc') {
            return $query->orderByDesc($sortCol)->orderBy($secondarySort);
        }

        if ($sortCol === 'purchase_date') {
            $query->orderByRaw('purchase_date is null');
        }

        return $query->orderBy($sortCol)->orderBy($secondarySort);
    }
}
