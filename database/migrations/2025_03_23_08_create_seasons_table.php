<?php

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
        Schema::create('seasons', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('id')->unsigned()->primary();
            $table->string('_id')->unique();
            $table->integer('series_id')->unsigned();
            $table->string('imdb_id', 10)->unique()->nullable();
            $table->string('name');
            $table->text('overview');
            $table->date('air_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('season_number')->unsigned();
            $table->string('poster_path');
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
