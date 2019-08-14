<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksChannelsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('uid', 50)->unique();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_channels');
    }
}
