<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntryAiReviewResultsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_ai_review_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('entry_id');
            $table->string('jobid');
            $table->string('ali_status')->nullable();
            $table->string('code')->nullable();
            $table->string('message')->nullable();
            $table->string('suggestion')->nullable();
            $table->string('abnormal_modules')->nullable();
            $table->string('label')->nullable();
            $table->unsignedInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_ai_review_results');
    }
}
