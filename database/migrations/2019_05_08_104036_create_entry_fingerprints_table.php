<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryFingerprintsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entry_fingerprints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->unique();
            $table->string('job_id', 50)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('entry_id')->references('id')->on('entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('entry_fingerprints');
    }
}
