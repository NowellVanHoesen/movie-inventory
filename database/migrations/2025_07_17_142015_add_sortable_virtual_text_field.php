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
        Schema::table('movies', function(Blueprint $table) {
            $table->string('title_sortable')->virtualAs("case
            WHEN LEFT(title, 2) = 'A ' THEN SUBSTR(title, 3)
            WHEN LEFT(title, 3) = 'An ' THEN SUBSTR(title, 4)
            WHEN LEFT(title, 4) = 'The ' THEN SUBSTR(title, 5)
            ELSE title
            END")->index();
        });

        Schema::table('movie_collections', function(Blueprint $table) {
            $table->string('name_sortable')->virtualAs("case
            WHEN LEFT(name, 2) = 'A ' THEN SUBSTR(name, 3)
            WHEN LEFT(name, 3) = 'An ' THEN SUBSTR(name, 4)
            WHEN LEFT(name, 4) = 'The ' THEN SUBSTR(name, 5)
            ELSE name
            END")->index();
        });

        Schema::table('series', function(Blueprint $table) {
            $table->string('name_sortable')->virtualAs("case
            WHEN LEFT(name, 2) = 'A ' THEN SUBSTR(name, 3)
            WHEN LEFT(name, 3) = 'An ' THEN SUBSTR(name, 4)
            WHEN LEFT(name, 4) = 'The ' THEN SUBSTR(name, 5)
            ELSE name
            END")->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function($table) {
            $table->dropColumn('title_sortable');
        });

        Schema::table('movie_collections', function($table) {
            $table->dropColumn('name_sortable');
        });

        Schema::table('series', function($table) {
            $table->dropColumn('name_sortable');
        });
    }
};
