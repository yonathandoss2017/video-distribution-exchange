<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPlatformYoutubesVideoId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_youtubes', function (Blueprint $table) {
            $table->dropColumn('video_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_youtubes', function (Blueprint $table) {
            $table->char('video_id', 11);
        });
    }
}
