<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformYoutubesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_youtubes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->char('video_id', 11);
            $table->string('channel_id', 32);
            $table->string('playlist_id', 40)->nullable();
            $table->integer('plays')->unsigned()->nullable();
            $table->integer('views')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('status')->default(2);
            $table->dateTime('published_at')->nullable();
            $table->integer('thumbnail_width')->unsigned()->default(0);
            $table->string('thumbnail_url')->nullable();
            $table->dateTime('ingested_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_youtubes');
    }
}
