# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

**Start all services (server + queue + logs + vite):**
```bash
composer run dev
```

**Frontend only:**
```bash
npm run dev       # development
npm run build     # production build
```

**Testing (Pest):**
```bash
php artisan test                                    # all tests
php artisan test tests/Feature/MoviesControllerTest.php   # single file
php artisan test --filter="it creates a movie"     # single test by name
php artisan test --coverage                         # with coverage
```

**Browser/Dusk tests must be run via `php artisan dusk`, not `php artisan test tests/Browser/...`.** Dusk tests drive the live Herd-served site, which reads the app's normal `.env` — only the `dusk` command performs Laravel's `.env` ↔ `.env.dusk.local` swap needed to point that live site at the dedicated `movie_inventory_dusk` database (see `tests/DuskTestCase.php`). Running them via `php artisan test` instead silently exercises whatever `movie_inventory` currently is.

**Restoring a database from a full SQL dump:**
```bash
php artisan db:restore-dump /path/to/dump.sql --database=movie_inventory_dusk
```
Drops every table in the named database and rebuilds it from a phpMyAdmin-style dump (schema + data for every table). Use this to refresh `movie_inventory_dusk` (the Dusk test baseline) or `movie_inventory` (local dev) from a fresh export — `--database` is required and it always confirms before running (`--force` to skip). It is never run automatically; re-run it by hand whenever you want a database resynced. Note: because `tests/DuskTestCase.php` deliberately skips Laravel's automatic `migrate:fresh` for `movie_inventory_dusk` (so `$exceptTables` can keep the imported dataset alive across a whole Dusk run), that database's *schema* only ever updates via this command — after adding a migration, re-run it with a fresh export to pick up the change.

**Code style (Laravel Pint):**
```bash
./vendor/bin/pint        # fix all files
./vendor/bin/pint --test  # check only
```

**Database:**
```bash
php artisan migrate
php artisan migrate:fresh --seed
```

## Architecture Overview

This is a **Laravel 12 + Vue 3 + Inertia.js 2** application for tracking a personal movie and TV series inventory. Data is sourced from the **TMDB API**.

### Backend (Laravel)

**Domain Models** — all in `app/Models/`:
- `Movie`, `Series`, `Season`, `Episode` — core media models using Spatie slug-based routing
- `CastMember` — actors/crew, many-to-many to Movie/Series/Season/Episode pivot tables with `character` and `order` columns
- `Genre`, `Certification`, `MediaType` — taxonomy models; `MediaType` is hierarchical (has `parent_id`)
- `MovieCollection` — movie franchises (e.g., the Marvel universe)

All slug-using models implement `HasSlug` via Spatie Sluggable; route model binding resolves by slug.

**Controllers** — `app/Http/Controllers/`:
- `MoviesController` — full CRUD; `create()` calls TMDB to prefill form data
- `SeriesController` — same pattern plus `showSeason()` / `showEpisode()` nested routes
- `HomeController`, `SearchController`, `CastMemberController`, `MovieCollectionController`

**TMDB integration** is centralized in `app/Traits/InteractsWithTMDB.php`. Controllers and Jobs use this trait for all API calls (search, detail, cast, recommendations).

**Job Queue** (`app/Jobs/`) handles async TMDB data ingestion after a movie or series is saved:
- `processSeries` → orchestrates a chain/batch: `processSeason` → `processEpisode` per season
- Separate jobs for attaching cast: `processMovieCastMembers`, `processSeriesCastMembers`, `processSeasonCastMembers`, `processEpisodeCastMembers`, `processMovieCollection`

**Shared data** is injected via `app/Http/Middleware/HandleInertiaRequests.php`:
- `auth.user`, `appName`, `placeholderPoster`, `placeholderStill`

**API Resources** (`app/Http/Resources/`) — transform models to JSON before passing to Inertia pages.

### Frontend (Vue 3 + Inertia)

**Layout**: `resources/js/layouts/Layout.vue` wraps all pages with Navigation and Footer.

**Pages** live in `resources/js/Pages/`:
- `Movies/Index.vue` — infinite scroll list with genre filtering and sorting (preferences persisted to sessionStorage)
- `Movies/MovieModal.vue` — detail view rendered as an Inertia modal
- `Home.vue`, `Welcome.vue`, `Movies/Show.vue`, `Movies/Collections/Index.vue`
- Auth pages under `Pages/Auth/`

**Modal system**: `BaseModal.vue` + `Modal.vue` — attached in the main layout. Movie detail routes open as modals without a full page reload. The `HandleInertiaRequests` middleware returns `null` for asset version on modal requests to prevent asset reloads.

**Key components**: `MoviePoster.vue`, `SeriesPoster.vue`, `CastMembers.vue`, `MovieFilterDropdown.vue`, `ItemPoster.vue`

### CSS / Tailwind

Tailwind v4 configured via `@tailwindcss/vite`; no `tailwind.config.js` — all config lives in `resources/css/app.css` using CSS custom properties. Custom theme palette: `cold-steel`, `green-check`, `red-heart`.

### Testing

Tests use **Pest 4**. Feature/Unit tests use `RefreshDatabase` on a dedicated MySQL database (`movie_inventory_tests`); the `tests/Pest.php` helper provides `loginAsUser(?User $user = null)` for auth context. TMDB HTTP calls should be faked via `Http::fake()` in tests.

Browser/Dusk tests (`tests/Browser/`) use `DatabaseTruncation` against a **separate** dedicated database, `movie_inventory_dusk`, restored from a full personal-collection SQL dump via `php artisan db:restore-dump` (see Development Commands above) rather than seeded per-test. It's kept separate from `movie_inventory_tests` because `RefreshDatabase`/`DatabaseTruncation` share a process-wide "already migrated" flag that triggers an unconditional `migrate:fresh` the first time either runs in a process — sharing one database would let a Feature-test run wipe the Dusk baseline. `tests/DuskTestCase.php` repoints this process's own Eloquent queries at `movie_inventory_dusk` (so assertions in test files see the same data the live site does) and excepts the bulk content tables from truncation so the imported dataset survives the whole run. `users` is intentionally left truncated between tests — since MySQL's `TRUNCATE` resets `AUTO_INCREMENT`, a freshly created `User::factory()->create()` in any given test reliably lands back on id 1.

### Key Conventions

- Route model binding resolves by **slug** (not `id`) for Movie, Series, CastMember, MovieCollection
- Pivot tables for cast store `character` (string) and `order` (int) alongside the FK pair
- Sortable/searchable fields have `_normalized` and `_sortable` column variants on the main tables
- Queue connection is `database`; always run `php artisan queue:listen` during local dev (included in `composer run dev`)
