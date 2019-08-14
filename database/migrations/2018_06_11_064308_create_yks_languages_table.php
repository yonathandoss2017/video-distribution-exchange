<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('yt_code', 30)->nullable();
            $table->string('yt_name', 100)->nullable();
            $table->string('dm_code', 30)->nullable();
            $table->string('dm_name', 100)->nullable();
            $table->string('ivs_code', 30)->nullable();
            $table->string('ivs_name', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_languages');
    }
}
