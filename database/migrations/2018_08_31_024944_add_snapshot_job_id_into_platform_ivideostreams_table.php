<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSnapshotJobIdIntoPlatformIvideostreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->string('snapshot_job_id')->nullable()->after('transcode_job_id');
            $table->boolean('snapshot_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->dropColumn('snapshot_job_id');
            $table->dropColumn('snapshot_status');
        });
    }
}
