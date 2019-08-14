<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndpointFieldIntoPlatformOauthTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_oauth', function (Blueprint $table) {
            $table->string('oss_endpoint', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_oauth', function (Blueprint $table) {
            $table->dropColumn('oss_endpoint');
        });
    }
}
