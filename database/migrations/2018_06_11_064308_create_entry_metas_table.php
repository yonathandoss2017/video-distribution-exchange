<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryMetasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index('entry_metas_entry_id_foreign');
            $table->text('tags', 65535)->nullable();
            $table->enum('video_type', ['movie_feature', 'movie_clip', 'series_container', 'series_episode', 'series_clip', 'standalone'])->nullable();
            $table->text('director', 65535)->nullable();
            $table->text('stars', 65535)->nullable();
            $table->enum('genre', ['animals', 'animation', 'documentary', 'drama', 'entertainment', 'fashion', 'food', 'film', 'gaming', 'kids', 'lifestyle', 'music', 'news', 'sports', 'tv', 'variety'])->nullable();
            $table->string('region', 100)->nullable();
            $table->enum('privacy', ['public', 'protect', 'private'])->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_metas');
    }
}
