<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('playlist_property', function (Blueprint $table) {
            $table->integer('property_id')->unsigned();
            $table->integer('playlist_id')->unsigned()->index('playlist_property_playlist_id_foreign');
            $table->timestamps();
            $table->string('status_bak', 10)->nullable()->default('pending');
            $table->integer('cp_property_id')->nullable();
            $table->string('playlist_name', 256)->nullable();
            $table->string('thumbnail_path', 512)->nullable();
            $table->primary(['property_id', 'playlist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('playlist_property');
    }
}
