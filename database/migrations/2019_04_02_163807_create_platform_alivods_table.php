<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformAlivodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_alivods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->string('video_id', 100)->nullable();
            $table->string('cover_id', 100)->nullable();
            $table->bigInteger('file_size_in_byte')->unsigned()->nullable();
            $table->bigInteger('disk_space_in_byte')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('platform_alivods');
    }
}
