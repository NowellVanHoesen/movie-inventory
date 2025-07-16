<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seriesSeeder = 'database/sql-files/series.sql';
        $seasonSeeder = 'database/sql-files/seasons.sql';
        $episodeSeeder = 'database/sql-files/episodes.sql';
        $castSeeder = 'database/sql-files/cast_members.sql';
        $seriesCastSeeder = 'database/sql-files/cast_member_series.sql';
        $seasonCastSeeder = 'database/sql-files/cast_member_season.sql';
        $episodeCastSeeder = 'database/sql-files/cast_member_episode.sql';

        DB::statement(file_get_contents($seriesSeeder));
        DB::statement(file_get_contents($seasonSeeder));
        DB::statement(file_get_contents($episodeSeeder));
        DB::statement(file_get_contents($castSeeder));
        DB::statement(file_get_contents($seriesCastSeeder));
        DB::statement(file_get_contents($seasonCastSeeder));
        DB::statement(file_get_contents($episodeCastSeeder));
    }
}
