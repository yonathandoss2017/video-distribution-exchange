<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPropertyIdInPlaylistEvidenceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlist_evidence_requests', function (Blueprint $table) {
            $table->integer('property_id')->unsigned()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlist_evidence_requests', function (Blueprint $table) {
            $table->dropColumn('property_id');
        });
    }
}
