<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostFeedWpPostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('post_feed_wp_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_feed_id')->unsigned();
            $table->integer('wp_post_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('platformable_id')->unsigned()->nullable();
            $table->string('platformable_type', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('post_feed_wp_posts');
    }
}
