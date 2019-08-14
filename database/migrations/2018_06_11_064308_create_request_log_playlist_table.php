<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogPlaylistTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('request_log_playlist', function (Blueprint $table) {
            $table->integer('request_log_id')->unsigned()->index('request_log_playlist_request_log_id_foreign');
            $table->integer('playlist_id')->unsigned()->index('request_log_playlist_playlist_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('request_log_playlist');
    }
}
