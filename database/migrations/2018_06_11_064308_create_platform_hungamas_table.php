<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformHungamasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_hungamas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index('platform_hangamas_entry_id_index');
            $table->integer('video_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('product_name');
            $table->string('thumbnail_url', 512)->nullable();
            $table->boolean('status')->default(2);
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('ingested_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_hungamas');
    }
}
