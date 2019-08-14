<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksVideosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_videos', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('uid', 50)->unique();
            $table->integer('channel_id')->unsigned()->nullable();
            $table->string('title', 100)->nullable();
            $table->text('description', 65535)->nullable();
            $table->text('tags', 65535)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('notified')->default(0);
            $table->timestamps();
            $table->text('captions', 65535)->nullable();
            $table->string('def_lang', 30)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('thumbnail_width')->unsigned()->default(0);
            $table->string('thumbnail_url')->nullable();
            $table->string('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_videos');
    }
}
