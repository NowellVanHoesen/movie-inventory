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
        Schema::create('movie_collections', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->integer('id')->unsigned()->primary();
            $table->string('name');
            $table->text('overview');
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('slug')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_collections');
    }
};
