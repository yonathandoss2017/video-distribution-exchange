<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAiReviewVideoResultsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ai_review_video_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('review_id')->default(0);
            $table->string('terrorism_label')->nullable();
            $table->string('terrorism_score')->nullable();
            $table->string('porn_label')->nullable();
            $table->string('porn_score')->nullable();
            $table->string('timestamp')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ai_review_video_results');
    }
}
