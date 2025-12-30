<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoviesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movieSeeder = 'database/sql-files/movies.sql';
        $movieGenresSeeder = 'database/sql-files/genre_movie.sql';
        $movieMediaTypeSeeder = 'database/sql-files/media_type_movie.sql';
        $castSeeder = 'database/sql-files/cast_members.sql';
        $movieCastSeeder = 'database/sql-files/cast_member_movie.sql';
        $movieCollectionSeeder = 'database/sql-files/movie_collections.sql';

        DB::statement(file_get_contents($movieSeeder));
        DB::statement(file_get_contents($movieGenresSeeder));
        DB::statement(file_get_contents($movieMediaTypeSeeder));
        DB::statement(file_get_contents($castSeeder));
        DB::statement(file_get_contents($movieCastSeeder));
        DB::statement(file_get_contents($movieCollectionSeeder));
    }
}
