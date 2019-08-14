<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->foreign('requester_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->dropForeign('request_logs_requester_user_id_foreign');
        });
    }
}
