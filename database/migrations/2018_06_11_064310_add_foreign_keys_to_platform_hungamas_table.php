<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPlatformHungamasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_hungamas', function (Blueprint $table) {
            $table->foreign('entry_id', 'platform_hangamas_entry_id_foreign')->references('id')->on('entries')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_hungamas', function (Blueprint $table) {
            $table->dropForeign('platform_hangamas_entry_id_foreign');
        });
    }
}
