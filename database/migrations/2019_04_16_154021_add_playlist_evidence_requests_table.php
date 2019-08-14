<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaylistEvidenceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('playlist_evidence_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('playlist_id')->unsigned()->index();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->unsigned()->default(0);
            $table->integer('updated_by')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('playlist_evidence_requests');
    }
}
