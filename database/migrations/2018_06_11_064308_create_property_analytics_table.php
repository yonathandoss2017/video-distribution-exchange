<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('property_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->index();
            $table->string('name');
            $table->boolean('site');
            $table->string('page');
            $table->integer('highest_active_user')->default(0);
            $table->integer('active_user')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('property_analytics');
    }
}
