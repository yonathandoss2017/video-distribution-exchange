<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPropertyContentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('property_contents', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('property_contents', function (Blueprint $table) {
            $table->dropForeign('property_contents_property_id_foreign');
        });
    }
}
