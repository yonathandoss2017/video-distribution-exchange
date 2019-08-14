<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoRegionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('geo_regions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('region_code', 3)->default('')->unique();
            $table->string('area');
            $table->integer('parent_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('geo_regions');
    }
}
