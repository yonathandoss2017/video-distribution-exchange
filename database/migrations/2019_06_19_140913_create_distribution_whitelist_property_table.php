<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributionWhitelistPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('distribution_whitelist_property', function (Blueprint $table) {
            $table->integer('property_id')->unsigned()->index();
            $table->integer('tod_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('distribution_whitelist_property');
    }
}
