<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTomIdFieldIntoPlaylists extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->unsignedInteger('tom_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn('tom_id');
        });
    }
}
