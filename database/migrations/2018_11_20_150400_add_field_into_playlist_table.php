<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldIntoPlaylistTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->boolean('dpp_status')->nullable();
            $table->dateTime('dpp_created_at')->nullable();
            $table->dateTime('dpp_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn(['dpp_status', 'dpp_created_at', 'dpp_updated_at']);
        });
    }
}
