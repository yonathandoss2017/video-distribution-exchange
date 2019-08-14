<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobIdIntoPlatformAlivodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_alivods', function (Blueprint $table) {
            $table->string('job_id', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_alivods', function (Blueprint $table) {
            $table->dropColumn('job_id');
        });
    }
}
