<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPlatformDailymotionsVideoId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_dailymotions', function (Blueprint $table) {
            $table->dropColumn('video_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_dailymotions', function (Blueprint $table) {
            $table->char('video_id', 7);
        });
    }
}
