<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngestionAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ingestion_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->integer('youtube_success')->default(0);
            $table->integer('youtube_failed')->default(0);
            $table->integer('direct_success')->default(0);
            $table->integer('direct_failed')->default(0);
            $table->integer('rss_success')->default(0);
            $table->integer('rss_failed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('ingestion_analytics');
    }
}
