<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToYksTimeFeedsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('yks_time_feeds', function (Blueprint $table) {
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('yks_time_feeds', function (Blueprint $table) {
            $table->dropForeign('yks_time_feeds_playlist_id_foreign');
            $table->dropForeign('yks_time_feeds_property_id_foreign');
        });
    }
}
