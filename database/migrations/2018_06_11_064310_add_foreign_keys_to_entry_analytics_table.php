<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEntryAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entry_analytics', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entry_analytics', function (Blueprint $table) {
            $table->dropForeign('entry_analytics_entry_id_foreign');
            $table->dropForeign('entry_analytics_property_id_foreign');
        });
    }
}
