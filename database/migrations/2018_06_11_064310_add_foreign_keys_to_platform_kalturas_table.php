<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPlatformKalturasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_kalturas', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_kalturas', function (Blueprint $table) {
            $table->dropForeign('platform_kalturas_entry_id_foreign');
        });
    }
}
