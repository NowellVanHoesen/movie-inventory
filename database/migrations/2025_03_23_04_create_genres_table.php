<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $initial_genres = [
        ['id' => 12, 'name' => 'Adventure'],
        ['id' => 14, 'name' => 'Fantasy'],
        ['id' => 16, 'name' => 'Animation'],
        ['id' => 18, 'name' => 'Drama'],
        ['id' => 27, 'name' => 'Horror'],
        ['id' => 28, 'name' => 'Action'],
        ['id' => 35, 'name' => 'Comedy'],
        ['id' => 36, 'name' => 'History'],
        ['id' => 37, 'name' => 'Western'],
        ['id' => 53, 'name' => 'Thriller'],
        ['id' => 80, 'name' => 'Crime'],
        ['id' => 99, 'name' => 'Documentary'],
        ['id' => 878, 'name' => 'Science Fiction'],
        ['id' => 9648, 'name' => 'Mystery'],
        ['id' => 10402, 'name' => 'Music'],
        ['id' => 10749, 'name' => 'Romance'],
        ['id' => 10751, 'name' => 'Family'],
        ['id' => 10752, 'name' => 'War'],
        ['id' => 10759, 'name' => 'Action & Adventure'],
        ['id' => 10762, 'name' => 'Kids'],
        ['id' => 10763, 'name' => 'News'],
        ['id' => 10764, 'name' => 'Reality'],
        ['id' => 10765, 'name' => 'Sci-Fi & Fantasy'],
        ['id' => 10766, 'name' => 'Soap'],
        ['id' => 10767, 'name' => 'Talk'],
        ['id' => 10768, 'name' => 'War & Politics'],
        ['id' => 10770, 'name' => 'TV Movie'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('id')->unsigned()->primary();
            $table->string('name')->unique();
        });

        DB::table('genres')->insert($this->initial_genres);

        Schema::create('genre_movie', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('movie_id')->unsigned();
            $table->integer('genre_id')->unsigned();
            $table->primary(['movie_id', 'genre_id']);
            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
            $table->foreign('genre_id')->references('id')->on('genres')->cascadeOnDelete();
        });

        Schema::create('genre_series', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('series_id')->unsigned();
            $table->integer('genre_id')->unsigned();
            $table->primary(['series_id', 'genre_id']);
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
            $table->foreign('genre_id')->references('id')->on('genres')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
        Schema::dropIfExists('genre_movies');
        Schema::dropIfExists('genre_movie');
        Schema::dropIfExists('genre_series');
    }
};
