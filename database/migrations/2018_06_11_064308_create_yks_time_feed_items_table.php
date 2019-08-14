<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksTimeFeedItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_time_feed_items', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('guid');
            $table->integer('feed_id')->unsigned()->index('yks_time_feed_items_feed_id_foreign');
            $table->integer('entry_id')->unsigned()->nullable()->index('yks_time_feed_items_entry_id_foreign');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['guid', 'feed_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_time_feed_items');
    }
}
