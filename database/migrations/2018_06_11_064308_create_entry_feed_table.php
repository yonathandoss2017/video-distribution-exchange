<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryFeedTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_feed', function (Blueprint $table) {
            $table->integer('entry_id')->unsigned();
            $table->integer('feed_id')->unsigned()->index('entry_feed_feed_id_foreign');
            $table->primary(['entry_id', 'feed_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_feed');
    }
}
