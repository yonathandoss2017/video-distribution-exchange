<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDownloadUrlToDownloadPathFromPlatformIvideostreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->renameColumn('download_url', 'download_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->renameColumn('download_path', 'download_url');
        });
    }
}
