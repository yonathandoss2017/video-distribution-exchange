<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoComparesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('video_compares', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->index();
            $table->string('title', 256);
            $table->string('video_url', 256);
            $table->string('job_id', 60)->nullable();
            $table->integer('duration_ms')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('video_compares');
    }
}
