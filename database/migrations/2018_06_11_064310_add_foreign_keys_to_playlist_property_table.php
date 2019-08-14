<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPlaylistPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlist_property', function (Blueprint $table) {
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlist_property', function (Blueprint $table) {
            $table->dropForeign('playlist_property_playlist_id_foreign');
            $table->dropForeign('playlist_property_property_id_foreign');
        });
    }
}
