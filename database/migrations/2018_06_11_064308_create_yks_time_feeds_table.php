<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksTimeFeedsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_time_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('playlist_id')->unsigned()->index('yks_time_feeds_playlist_id_foreign');
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('property_id')->unsigned()->nullable()->index('yks_time_feeds_property_id_foreign');
            $table->smallInteger('type')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_time_feeds');
    }
}
