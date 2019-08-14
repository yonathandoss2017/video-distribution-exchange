<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToYksThirdPartiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('yks_third_parties', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('yks_third_parties', function (Blueprint $table) {
            $table->dropForeign('yks_third_parties_entry_id_foreign');
            $table->dropForeign('yks_third_parties_property_id_foreign');
        });
    }
}
