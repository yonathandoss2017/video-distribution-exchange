<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryAnzhengEvidencesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_anzheng_evidences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->unique();
            $table->char('case_id', 25);
            $table->string('receipt', 255)->default('');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->foreign('entry_id')->references('id')->on('entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('entry_anzheng_evidences');
    }
}
