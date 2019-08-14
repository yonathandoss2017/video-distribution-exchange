<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->index();
            $table->string('name', 256);
            $table->text('description', 65535)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('genre', 20)->nullable();
            $table->string('region', 20)->nullable();
            $table->string('video_type', 20)->nullable();
            $table->string('language', 20)->nullable();
            $table->boolean('published')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('thumbnail_path', 512)->nullable();
            $table->text('stars', 65535)->nullable();
            $table->dateTime('indexed_at_marketplace')->nullable();
            $table->boolean('priority')->default(1);
            $table->boolean('status')->default(0);
            $table->boolean('retry_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('playlists');
    }
}
