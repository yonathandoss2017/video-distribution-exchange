<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mobile_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->index();
            $table->string('name')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->string('thumbnail_url', 512)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('mobile_categories');
    }
}
