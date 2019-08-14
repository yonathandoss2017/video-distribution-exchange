<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnsRelateToPublishInPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->renameColumn('published', 'publish');
        });
        Schema::table('playlists', function (Blueprint $table) {
            $table->renameColumn('start_date', 'publish_start_date');
        });
        Schema::table('playlists', function (Blueprint $table) {
            $table->renameColumn('end_date', 'publish_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->renameColumn('publish', 'published');
        });
        Schema::table('playlists', function (Blueprint $table) {
            $table->renameColumn('publish_start_date', 'start_date');
        });
        Schema::table('playlists', function (Blueprint $table) {
            $table->renameColumn('publish_end_date', 'end_date');
        });
    }
}
