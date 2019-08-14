<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToEntryScenesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entry_scenes', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entry_scenes', function (Blueprint $table) {
            $table->dropForeign('entry_scenes_entry_id_foreign');
        });
    }
}
