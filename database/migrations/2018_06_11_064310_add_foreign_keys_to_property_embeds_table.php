<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPropertyEmbedsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('property_embeds', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('property_embeds', function (Blueprint $table) {
            $table->dropForeign('property_embeds_property_id_foreign');
        });
    }
}
