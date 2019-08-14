<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEntryPlaylistTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entry_playlist', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entry_playlist', function (Blueprint $table) {
            $table->dropForeign('entry_playlist_entry_id_foreign');
            $table->dropForeign('entry_playlist_playlist_id_foreign');
        });
    }
}
