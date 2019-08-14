<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPlaylistTermsOfDistributionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlist_terms_of_distribution', function (Blueprint $table) {
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('tod_id')->references('id')->on('terms_of_distributions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlist_terms_of_distribution', function (Blueprint $table) {
            $table->dropForeign('playlist_terms_of_distribution_playlist_id_foreign');
            $table->dropForeign('playlist_terms_of_distribution_tod_id_foreign');
        });
    }
}
