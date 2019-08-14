<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToRequestLogUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('request_log_user', function (Blueprint $table) {
            $table->foreign('request_log_id')->references('id')->on('request_logs')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('request_log_user', function (Blueprint $table) {
            $table->dropForeign('request_log_user_request_log_id_foreign');
            $table->dropForeign('request_log_user_user_id_foreign');
        });
    }
}
