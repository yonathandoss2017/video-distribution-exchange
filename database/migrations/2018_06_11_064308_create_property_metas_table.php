<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyMetasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('property_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->index('property_metas_property_id_foreign');
            $table->string('meta_name', 100);
            $table->string('meta_value', 512);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('property_metas');
    }
}
