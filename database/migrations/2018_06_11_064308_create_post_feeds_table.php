<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostFeedsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('post_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('playlist_id');
            $table->string('post_type', 10)->default('post');
            $table->string('post_status', 10)->default('draft');
            $table->text('post_category', 65535)->nullable();
            $table->timestamps();
            $table->integer('property_id')->unsigned();
            $table->softDeletes();
            $table->boolean('importing')->nullable();
            $table->boolean('sync_description')->default(1);
            $table->unique(['playlist_id', 'property_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('post_feeds');
    }
}
