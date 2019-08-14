<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMobileCategoryPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('mobile_category_playlists', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('mobile_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('mobile_category_playlists', function (Blueprint $table) {
            $table->dropForeign('mobile_category_playlists_category_id_foreign');
            $table->dropForeign('mobile_category_playlists_playlist_id_foreign');
        });
    }
}
