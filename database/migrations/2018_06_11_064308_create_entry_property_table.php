<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_property', function (Blueprint $table) {
            $table->integer('entry_id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->string('title', 256);
            $table->text('description', 65535)->nullable();
            $table->string('image_path', 512)->nullable();
            $table->timestamps();
            $table->unique(['property_id', 'entry_id'], 'entry_property_new_property_id_entry_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_property');
    }
}
