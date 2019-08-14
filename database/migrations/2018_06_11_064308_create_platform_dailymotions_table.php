<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformDailymotionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_dailymotions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->char('video_id', 7);
            $table->string('channel_id', 24)->nullable();
            $table->string('playlist_id', 6)->nullable();
            $table->string('user_id', 7)->nullable();
            $table->integer('plays')->unsigned()->nullable();
            $table->integer('views')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('dm_block_ads')->default(0);
            $table->boolean('status')->default(1);
            $table->dateTime('ingested_at')->nullable();
            $table->string('thumbnail_h120', 512)->nullable();
            $table->string('thumbnail_h180', 512)->nullable();
            $table->string('thumbnail_h360', 512)->nullable();
            $table->string('thumbnail_h480', 512)->nullable();
            $table->string('thumbnail_h720', 512)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_dailymotions');
    }
}
