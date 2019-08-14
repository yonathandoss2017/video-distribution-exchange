<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformLivestreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_livestreams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->integer('video_id')->unsigned()->index();
            $table->string('stream_url', 512);
            $table->string('thumbnail_url', 512)->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('stopped_at')->nullable();
            $table->boolean('status')->default(2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_livestreams');
    }
}
