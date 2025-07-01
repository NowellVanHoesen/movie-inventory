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
        Schema::table('movies', function($table) {
            $table->string('title_normalized')->virtualAs("regexp_replace(REPLACE(title, '-', ' '), '[^A-Za-z0-9 ]', '')")->index();
        });

        Schema::table('movie_collections', function($table) {
            $table->string('name_normalized')->virtualAs("regexp_replace(REPLACE(name, '-', ' '), '[^A-Za-z0-9 ]', '')")->index();
        });

        Schema::table('series', function($table) {
            $table->string('name_normalized')->virtualAs("regexp_replace(REPLACE(name, '-', ' '), '[^A-Za-z0-9 ]', '')")->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function($table) {
            $table->dropColumn('title_normalized');
        });

        Schema::table('movie_collections', function($table) {
            $table->dropColumn('name_normalized');
        });

        Schema::table('series', function($table) {
            $table->dropColumn('name_normalized');
        });
    }
};
