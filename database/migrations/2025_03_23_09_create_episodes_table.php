<?php

use App\Models\Season;
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
        Schema::create('episodes', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('id')->unsigned()->primary();
            $table->string('imdb_id', 10)->unique()->nullable();
            $table->string('name');
            $table->text('overview');
            $table->string('still_path');
            $table->date('air_date')->nullable();
            $table->integer('runtime')->unsigned()->nullable();
            $table->integer('episode_number')->unsigned();
            $table->integer('season_id')->unsigned();
            $table->foreign('season_id')->references('id')->on('seasons')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
