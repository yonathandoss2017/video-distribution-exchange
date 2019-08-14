<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEntryFeedTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entry_feed', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('feed_id')->references('id')->on('yks_video_feeds')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entry_feed', function (Blueprint $table) {
            $table->dropForeign('entry_feed_entry_id_foreign');
            $table->dropForeign('entry_feed_feed_id_foreign');
        });
    }
}
