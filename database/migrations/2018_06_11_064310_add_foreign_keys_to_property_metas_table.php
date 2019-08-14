<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPropertyMetasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('property_metas', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('property_metas', function (Blueprint $table) {
            $table->dropForeign('property_metas_property_id_foreign');
        });
    }
}
