<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostFeedConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('post_feed_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('website', 100);
            $table->string('client_key');
            $table->string('client_secret');
            $table->string('token', 100);
            $table->string('token_secret', 100);
            $table->timestamps();
            $table->integer('property_id')->unsigned()->unique();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('post_feed_configurations');
    }
}
