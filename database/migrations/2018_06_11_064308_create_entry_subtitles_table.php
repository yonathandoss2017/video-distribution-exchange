<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntrySubtitlesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_subtitles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('lang', 10)->nullable();
            $table->text('url', 65535)->nullable();
            $table->unique(['entry_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('entry_subtitles');
    }
}
