<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryScenesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_scenes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->string('name', 256)->nullable();
            $table->string('image_path', 512);
            $table->text('description', 65535)->nullable();
            $table->integer('start_in_seconds')->nullable();
            $table->integer('end_in_seconds')->nullable();
            $table->integer('dpp_duration')->nullable();
            $table->string('type', 32)->nullable();
            $table->string('locale', 256)->nullable();
            $table->string('suitable', 1000)->nullable();
            $table->string('blacklist', 1000)->nullable();
            $table->string('keywords', 1000)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_scenes');
    }
}
