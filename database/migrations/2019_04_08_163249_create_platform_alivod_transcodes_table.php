<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformAlivodTranscodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_alivod_transcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('platform_alivod_id')->nullable()->index();
            $table->bigInteger('size')->unsigned()->nullable();
            $table->char('definition', 2)->nullable();
            $table->unsignedTinyInteger('fps')->nullable();
            $table->float('duration', 8, 2)->nullable();
            $table->unsignedInteger('bitrate')->nullable();
            $table->string('format')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('platform_alivod_transcodes');
    }
}
