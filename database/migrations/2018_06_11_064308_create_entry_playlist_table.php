<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryPlaylistTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_playlist', function (Blueprint $table) {
            $table->integer('entry_id')->unsigned();
            $table->integer('playlist_id')->unsigned()->index('entry_playlist_playlist_id_foreign');
            $table->primary(['entry_id', 'playlist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_playlist');
    }
}
