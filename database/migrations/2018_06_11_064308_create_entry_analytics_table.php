<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned();
            $table->integer('property_id')->unsigned()->index('entry_analytics_property_id_foreign');
            $table->integer('views')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['entry_id', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_analytics');
    }
}
