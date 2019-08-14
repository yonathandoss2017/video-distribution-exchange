<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToRequestLogPlaylistTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('request_log_playlist', function (Blueprint $table) {
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('request_log_id')->references('id')->on('request_logs')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('request_log_playlist', function (Blueprint $table) {
            $table->dropForeign('request_log_playlist_playlist_id_foreign');
            $table->dropForeign('request_log_playlist_request_log_id_foreign');
        });
    }
}
