# Movie Inventory

A personal app for tracking a movie and TV series collection. You search for a title, pick the formats you own (or add it to a wishlist), and the app pulls the rest — posters, cast, genres, certifications, seasons and episodes — from the [TMDB](https://www.themoviedb.org/) API.

Built with Laravel 12, Vue 3 and Inertia.js 2.

> This product uses the TMDB API but is not endorsed or certified by TMDB.

## Features

- **Movies** — poster grid with infinite scroll, genre filtering and sorting, separate purchased and wishlist views, and a detail view that opens as a modal
- **Series** — a full detail page with seasons, episodes and per-episode cast
- **Movie collections** — franchises grouped together (e.g. the Marvel universe)
- **Cast members** — a page per person listing the movies and series they appear in
- **Search** across the inventory
- **Adding titles from TMDB** — search TMDB, save the title, and queued jobs fetch cast, collections, seasons and episodes in the background
- **Public browsing, authenticated editing** — anyone can browse; adding, editing and deleting require a login

## Tech stack

| Layer    | Tools                                                                 |
| -------- | --------------------------------------------------------------------- |
| Backend  | PHP 8.4, Laravel 12, Inertia.js 2, Spatie Sluggable, Ziggy            |
| Frontend | Vue 3, Tailwind CSS v4, Headless UI, Font Awesome, Vite 7             |
| Data     | MySQL, Laravel `database` queue, TMDB API                             |
| Testing  | Pest 4 (Feature/Unit), Laravel Dusk (Browser)                         |
| Tooling  | Laravel Pint, Prettier                                                |

## Requirements

- PHP 8.4+ and Composer
- Node.js and npm
- MySQL
- A TMDB API read access token (a free account at [themoviedb.org](https://www.themoviedb.org/settings/api))

## Getting started

```bash
git clone git@github.com:NowellVanHoesen/movie-inventory.git
cd movie-inventory

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` to point `DB_*` at a MySQL database (local dev uses `movie_inventory`) and set the TMDB token:

| Variable            | Required | Default                      | Purpose                                  |
| ------------------- | -------- | ---------------------------- | ---------------------------------------- |
| `TMDB_API_AUTH_KEY` | yes      | —                            | TMDB API read access token (bearer token) |
| `TMDB_API_BASE_URL` | no       | `https://api.themoviedb.org` | TMDB API host                            |
| `TMDB_API_VERSION`  | no       | `3`                          | TMDB API version                         |

Then create the schema and start everything:

```bash
php artisan migrate
composer run dev
```

`composer run dev` runs the PHP server, the queue worker, the log viewer and Vite together. The queue worker matters: TMDB data for a newly added movie or series is ingested by queued jobs, so nothing arrives without one running.

`php artisan migrate:fresh --seed` loads sample movie and series data from the SQL files in `database/sql-files/`.

There is no public registration route, so create your user by hand, for example in `php artisan tinker`:

```php
App\Models\User::factory()->create(['email' => 'you@example.com', 'password' => 'secret']);
```

## Development

```bash
composer run dev   # server + queue + logs + vite
npm run dev        # Vite only
npm run build      # production build
```

If the site is served by Laravel Herd, rebuild Laravel's caches after each `npm run build` so it picks up the new assets:

```bash
php artisan optimize:clear
php artisan optimize
```

Code style is enforced with Laravel Pint:

```bash
./vendor/bin/pint         # fix
./vendor/bin/pint --test  # check only
```

## Testing

Tests use three separate MySQL databases:

| Database                | Used by                                                        |
| ----------------------- | -------------------------------------------------------------- |
| `movie_inventory`       | local development                                              |
| `movie_inventory_tests` | Feature and Unit tests (`RefreshDatabase`)                     |
| `movie_inventory_dusk`  | Browser tests, restored from a full SQL dump of the collection |

```bash
php artisan optimize:clear                                   # tests need an uncached config

php artisan test --testsuite=Feature,Unit                    # all Feature + Unit tests
php artisan test tests/Feature/movies/MoviesDestroyTest.php  # one file
php artisan test --filter="it creates a movie"               # one test by name

php artisan dusk                                             # all Browser tests
```

Three things to know before the first run:

- **Never run a bare `php artisan test`.** It also picks up the Browser suite, which drives the live site against `movie_inventory` and changes real data. Always scope it with `--testsuite=Feature,Unit` or a path.
- **Clear the config cache first.** With a cached config, `phpunit.xml`'s overrides are ignored and Dusk's `.env.dusk.local` swap has no effect. Run `php artisan optimize` again when you are done.
- **Run Browser tests with `php artisan dusk` only.** They exercise the built assets, so run `npm run build` after any frontend change.

Create or refresh the Dusk database from a phpMyAdmin-style dump:

```bash
php artisan db:restore-dump /path/to/dump.sql --database=movie_inventory_dusk
```

If every Dusk test fails with a ChromeDriver version error, run `php artisan dusk:chrome-driver --detect`.

## Project structure

```
app/
  Models/                       Movie, Series, Season, Episode, CastMember, Genre, …
  Http/Controllers/             Movies, Series, Search, CastMember, MovieCollection, …
  Http/Resources/               API resources passed to Inertia pages
  Jobs/                         queued TMDB ingestion (ProcessSeries, ProcessSeason, …)
  Traits/InteractsWithTMDB.php  every TMDB API call
  Console/Commands/             db:restore-dump
resources/
  js/Pages/                     Inertia pages (Movies, Series, Search, CastMembers, Auth, …)
  js/Components/                shared Vue components
  js/Layouts/                   page layout
  css/app.css                   Tailwind v4 config and theme
tests/
  Feature/  Unit/               Pest tests
  Browser/                      Dusk tests
```

[CLAUDE.md](CLAUDE.md) has the detailed architecture notes and conventions.

## Deployment

Publishing a GitHub release triggers [.github/workflows/deploy.yml](.github/workflows/deploy.yml), which connects to the host over SSH and runs [.github/workflows/deploy.sh](.github/workflows/deploy.sh) with the release tag. The script puts the site in maintenance mode, checks out that tag, installs Composer and npm dependencies, runs migrations, rebuilds Laravel's caches and brings the site back up.

Renaming a job class breaks queued payloads that reference the old name, so deploy those changes with an empty queue.

## License

MIT
