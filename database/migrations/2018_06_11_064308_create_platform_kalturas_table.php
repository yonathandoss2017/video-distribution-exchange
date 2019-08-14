<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformKalturasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_kalturas', function (Blueprint $table) {
            $table->increments('id');
            $table->char('video_id', 10);
            $table->integer('entry_id')->unsigned()->index();
            $table->char('partner_id', 7);
            $table->string('user_id', 128);
            $table->string('creator_id', 128);
            $table->integer('plays')->unsigned();
            $table->integer('views')->unsigned();
            $table->string('thumbnail_url', 512);
            $table->string('download_url', 256);
            $table->string('data_url', 256);
            $table->boolean('status');
            $table->string('session', 256)->nullable();
            $table->dateTime('session_expire_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_kalturas');
    }
}
