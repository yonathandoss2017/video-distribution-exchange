<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileCategoryPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mobile_category_playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->index('mobile_category_playlists_category_id_foreign');
            $table->integer('playlist_id')->unsigned()->index('mobile_category_playlists_playlist_id_foreign');
            $table->integer('position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('mobile_category_playlists');
    }
}
