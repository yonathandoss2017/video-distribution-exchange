<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPlatformIvideostreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->string('transcode_template')->nullable()->after('snapshot_job_id')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->dropColumn(['transcode_template']);
        });
    }
}
