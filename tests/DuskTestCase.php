<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Dedicated database for Dusk/Browser tests, restored from a full data dump via
     * `php artisan db:restore-dump` and left alone otherwise. It's intentionally
     * separate from phpunit.xml's `movie_inventory_tests` (used by Feature tests):
     * RefreshDatabase runs `migrate:fresh` once per process, which would wipe a
     * shared database back down to empty tables the first time a Feature test ran.
     * `.env.dusk.local`'s DB_DATABASE must match this so the live Herd-served site
     * (swapped in for the duration of `php artisan dusk`) reads the same data as
     * the Eloquent queries made from within these test methods.
     */
    protected const DUSK_DATABASE = 'movie_inventory_dusk';

    /**
     * Bulk content tables restored by `db:restore-dump`, kept alive across the whole
     * Dusk run so it survives as a stable baseline rather than being emptied by
     * DatabaseTruncation before every test. `certifications`, `genres`, `media_types`
     * are seeded by their migrations (not a seeder), so they need the same treatment.
     * `users` is deliberately left out: it's truncated normally between tests, and
     * since MySQL's TRUNCATE resets AUTO_INCREMENT, a freshly created user in any
     * given test reliably lands back on id 1.
     */
    protected array $exceptTables = [
        'certifications', 'genres', 'media_types',
        'movies', 'movie_collections', 'series', 'seasons', 'episodes',
        'cast_members', 'cast_member_movie', 'cast_member_series', 'cast_member_season', 'cast_member_episode',
        'genre_movie', 'genre_series',
        'media_type_movie', 'media_type_season', 'media_type_series',
    ];

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Point this process's own Eloquent/DB queries at the dedicated Dusk database,
     * overriding whatever phpunit.xml configured. This runs before DatabaseTruncation's
     * own setup (which needs the correct database to truncate the right tables), since
     * `refreshApplication()` is called ahead of trait setup in the test lifecycle.
     *
     * DatabaseTruncation shares Laravel's RefreshDatabaseState::$migrated flag with
     * RefreshDatabase: on the first test in a process it unconditionally runs
     * `migrate:fresh` (ignoring $exceptTables entirely) before ever reaching the
     * truncation logic, which would wipe this database's dump-restored data back down
     * to schema-only + migration-seeded lookup tables. Forcing it to true skips that
     * and goes straight to truncateTablesForAllConnections(), which does respect
     * $exceptTables. The tradeoff: this database's schema is only ever updated by
     * re-running `db:restore-dump` with a fresh export — it will never auto-migrate.
     */
    protected function refreshApplication()
    {
        parent::refreshApplication();

        config(['database.connections.mysql.database' => self::DUSK_DATABASE]);
        DB::purge('mysql');

        RefreshDatabaseState::$migrated = true;
    }

    public function newBrowser($driver)
    {
        return new Browser($driver);
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
