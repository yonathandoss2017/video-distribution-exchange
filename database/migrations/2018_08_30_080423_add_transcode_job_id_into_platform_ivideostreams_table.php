<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTranscodeJobIdIntoPlatformIvideostreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->string('transcode_job_id')->nullable()->after('size_in_bytes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->dropColumn('transcode_job_id');
        });
    }
}
