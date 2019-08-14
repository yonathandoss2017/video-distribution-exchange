<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryAnalyzesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_analyzes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned();
            $table->string('category_name');
            $table->string('frame_rect');
            $table->string('frame_time');
            $table->string('tag_name');
            $table->string('frame_rect_percentage', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_analyzes');
    }
}
