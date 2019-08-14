<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetadataMusicvideosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('metadata_musicvideos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->unique();
            $table->string('artist', 100)->nullable();
            $table->string('album', 100)->nullable();
            $table->smallInteger('year')->nullable();
            $table->string('genre', 100)->nullable();
            $table->string('language', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('metadata_musicvideos');
    }
}
