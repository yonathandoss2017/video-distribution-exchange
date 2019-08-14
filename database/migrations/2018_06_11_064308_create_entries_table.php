<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('property_id')->unsigned()->index();
            $table->string('name', 256);
            $table->text('description', 65535)->nullable();
            $table->enum('media_type', ['video', 'audio', 'image']);
            $table->integer('duration')->unsigned()->nullable();
            $table->string('thumbnail_url', 512)->nullable();
            $table->string('image_path', 512)->nullable();
            $table->string('platforms')->nullable();
            $table->timestamps();
            $table->integer('views')->unsigned()->default(0);
            $table->softDeletes();
            $table->string('status', 20)->default('processing');
            $table->dateTime('published_at')->nullable();
            $table->string('allowed_in', 1024)->nullable();
            $table->string('blocked_in', 1024)->nullable();
            $table->boolean('published')->default(0);
            $table->dateTime('indexed_at')->nullable();
            $table->dateTime('indexed_at_marketplace')->nullable();
            $table->integer('metaable_id')->nullable();
            $table->string('metaable_type', 100)->nullable();
            $table->string('source', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entries');
    }
}
