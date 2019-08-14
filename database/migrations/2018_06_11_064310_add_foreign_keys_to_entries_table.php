<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEntriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign('entries_property_id_foreign');
            $table->dropForeign('entries_user_id_foreign');
        });
    }
}
