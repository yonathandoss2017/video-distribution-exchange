<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOssOuterEndpointFieldInfoPlatformOauthTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_oauth', function (Blueprint $table) {
            $table->renameColumn('oss_endpoint', 'oss_internal_endpoint');
            $table->string('oss_outer_endpoint', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_oauth', function (Blueprint $table) {
            $table->renameColumn('oss_internal_endpoint', 'oss_endpoint');
            $table->dropColumn('oss_outer_endpoint');
        });
    }
}
