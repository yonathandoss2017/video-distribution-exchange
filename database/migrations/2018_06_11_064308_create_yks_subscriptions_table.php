<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id')->unsigned();
            $table->timestamps();
            $table->integer('playlist_id')->unsigned()->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->integer('feed_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_subscriptions');
    }
}
