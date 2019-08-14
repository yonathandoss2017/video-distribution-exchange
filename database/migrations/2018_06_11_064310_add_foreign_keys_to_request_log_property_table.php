<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToRequestLogPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('request_log_property', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('request_log_id')->references('id')->on('request_logs')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('request_log_property', function (Blueprint $table) {
            $table->dropForeign('request_log_property_property_id_foreign');
            $table->dropForeign('request_log_property_request_log_id_foreign');
        });
    }
}
