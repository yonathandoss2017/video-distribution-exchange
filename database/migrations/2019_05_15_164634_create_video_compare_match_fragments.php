<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoCompareMatchFragments extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('video_compare_match_fragments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('compare_result_id')->index();
            $table->integer('searchedVideoStartingMatchedPosition_ms')->nullable();
            $table->integer('matchedVideoStartingMatchedPosition_ms')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('video_compare_match_fragments');
    }
}
