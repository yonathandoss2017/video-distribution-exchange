<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPlatformLivestreamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_livestreams', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_livestreams', function (Blueprint $table) {
            $table->dropForeign('platform_livestreams_entry_id_foreign');
        });
    }
}
