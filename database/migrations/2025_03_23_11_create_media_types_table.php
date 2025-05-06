<?php

use App\Models\MediaType;
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
        Schema::create('media_types', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->id()->from(1);
            $table->integer('parent_id')->unsigned()->default(0);
            $table->string('name');
        });

        $digital = MediaType::firstOrCreate([
            'name' => 'Digital'
        ]);

        $physical = MediaType::firstOrCreate([
            'name' => 'Physical'
        ]);

        MediaType::firstOrCreate([
            'name' => 'Fandango',
            'parent_id' => $digital->id
        ]);

        MediaType::firstOrCreate([
            'name' => 'Apple TV',
            'parent_id' => $digital->id
        ]);

        MediaType::firstOrCreate([
            'name' => 'DVD',
            'parent_id' => $physical->id
        ]);

        MediaType::firstOrCreate([
            'name' => 'Blu-Ray',
            'parent_id' => $physical->id
        ]);

        MediaType::firstOrCreate([
            'name' => '4K',
            'parent_id' => $physical->id
        ]);

        MediaType::firstOrCreate([
            'name' => 'VHS',
            'parent_id' => $physical->id
        ]);


        Schema::create('media_type_movie', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('movie_id')->unsigned();
            $table->foreignIdFor(MediaType::class, 'media_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['movie_id','media_type_id']);
            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
        });

        Schema::create('media_type_season', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('season_id')->unsigned();
            $table->foreignIdFor(MediaType::class, 'media_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['season_id','media_type_id']);
            $table->foreign('season_id')->references('id')->on('seasons')->cascadeOnDelete();
        });

        Schema::create('media_type_series', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('series_id')->unsigned();
            $table->foreignIdFor(MediaType::class, 'media_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['series_id','media_type_id']);
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
       });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_types');
        Schema::dropIfExists('media_type_movie');
        Schema::dropIfExists('media_type_season');
        Schema::dropIfExists('media_type_series');
    }
};
