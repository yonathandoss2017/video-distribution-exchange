<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContainerFormatIntoPlatformIvideostreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->string('container_format')->default('mp4')->after('size_in_bytes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_ivideostreams', function (Blueprint $table) {
            $table->dropColumn('container_format');
        });
    }
}
