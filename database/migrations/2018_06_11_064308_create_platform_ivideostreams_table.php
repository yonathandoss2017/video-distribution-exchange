<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformIvideostreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_ivideostreams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->integer('video_id')->unsigned()->index();
            $table->string('thumbnail_url', 512)->nullable();
            $table->string('download_url', 512)->nullable();
            $table->bigInteger('size_in_bytes')->unsigned()->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('video_path', 256)->nullable();
            $table->dateTime('ingested_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_ivideostreams');
    }
}
