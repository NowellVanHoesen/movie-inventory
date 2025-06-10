<?php

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
        Schema::create('series', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('id')->unsigned()->primary();
            $table->string('imdb_id', 10)->unique()->nullable();
            $table->string('name');
            $table->string('original_name');
            $table->string('tagline');
            $table->text('overview');
            $table->string('homepage');
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('certification_id')->constrained();
            $table->date('first_air_date');
            $table->date('purchase_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
