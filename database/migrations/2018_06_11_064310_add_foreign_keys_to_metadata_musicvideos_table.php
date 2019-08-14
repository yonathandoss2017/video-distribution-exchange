<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMetadataMusicvideosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('metadata_musicvideos', function (Blueprint $table) {
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('metadata_musicvideos', function (Blueprint $table) {
            $table->dropForeign('metadata_musicvideos_entry_id_foreign');
        });
    }
}
