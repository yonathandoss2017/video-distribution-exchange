<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoCompareResultsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('video_compare_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('compare_id')->unsigned()->index();
            $table->integer('entry_id')->unsigned()->index();
            $table->string('confidence', 30)->nullable();
            $table->string('distortion', 30)->nullable();
            $table->integer('length_ms')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('video_compare_results');
    }
}
