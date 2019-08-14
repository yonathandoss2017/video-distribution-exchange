<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceUrlFieldIntoPlatformAlivodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_alivods', function (Blueprint $table) {
            $table->string('source_url', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_alivods', function (Blueprint $table) {
            $table->dropColumn('source_url');
        });
    }
}
