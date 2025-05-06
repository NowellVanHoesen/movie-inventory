<?php

use App\Models\CastMember;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Series;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cast_members', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('id')->unsigned()->primary();
            $table->string('name');
            $table->string('original_name');
            $table->string('profile_path');
        });

        Schema::create('cast_member_movie', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('cast_member_id')->unsigned();
            $table->integer('movie_id')->unsigned();
            $table->string('character');
            $table->integer('order');
            $table->primary(['cast_member_id','movie_id']);
            $table->foreign('cast_member_id')->references('id')->on('cast_members')->cascadeOnDelete();
            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
        });

        Schema::create('cast_member_series', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('cast_member_id')->unsigned();
            $table->integer('series_id')->unsigned();
            $table->string('character');
            $table->integer('order');
            $table->primary(['cast_member_id','series_id']);
            $table->foreign('cast_member_id')->references('id')->on('cast_members')->cascadeOnDelete();
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
        });

        Schema::create('cast_member_season', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('cast_member_id')->unsigned();
            $table->integer('season_id')->unsigned();
            $table->string('character');
            $table->integer('order');
            $table->primary(['cast_member_id','season_id']);
            $table->foreign('cast_member_id')->references('id')->on('cast_members')->cascadeOnDelete();
            $table->foreign('season_id')->references('id')->on('seasons')->cascadeOnDelete();
        });

        Schema::create('cast_member_episode', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('cast_member_id')->unsigned();
            $table->integer('episode_id')->unsigned();
            $table->string('character');
            $table->integer('order');
            $table->primary(['cast_member_id','episode_id']);
            $table->foreign('cast_member_id')->references('id')->on('cast_members')->cascadeOnDelete();
            $table->foreign('episode_id')->references('id')->on('episodes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast_members');
        Schema::dropIfExists('cast_member_movie');
        Schema::dropIfExists('cast_member_series');
        Schema::dropIfExists('cast_member_season');
        Schema::dropIfExists('cast_member_episode');
    }
};
