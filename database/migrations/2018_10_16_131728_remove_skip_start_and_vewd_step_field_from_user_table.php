<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSkipStartAndVewdStepFieldFromUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skip_start', 'vewd_step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
