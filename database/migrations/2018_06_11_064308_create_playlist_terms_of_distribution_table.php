<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistTermsOfDistributionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('playlist_terms_of_distribution', function (Blueprint $table) {
            $table->integer('playlist_id')->unsigned()->index();
            $table->integer('tod_id')->unsigned()->index();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('playlist_terms_of_distribution');
    }
}
