<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldInfoEntryPlaylistTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entry_playlist', function (Blueprint $table) {
            $table->boolean('dpp_requested')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entry_playlist', function (Blueprint $table) {
            $table->dropColumn('dpp_requested');
        });
    }
}
