<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MoviesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movieSeeder = 'database/sql-files/movies.sql';
        $castSeeder = 'database/sql-files/cast_members.sql';
        $movieCastSeeder = 'database/sql-files/cast_member_movie.sql';
        $movieCollectionSeeder = 'database/sql-files/movie_collections.sql';

        DB::statement(file_get_contents($movieSeeder));
        DB::statement(file_get_contents($castSeeder));
        DB::statement(file_get_contents($movieCastSeeder));
        DB::statement(file_get_contents($movieCollectionSeeder));
    }
}
