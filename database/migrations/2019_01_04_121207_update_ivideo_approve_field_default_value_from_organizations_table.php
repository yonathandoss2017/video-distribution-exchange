<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIvideoApproveFieldDefaultValueFromOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->boolean('allow_ivideoapprove')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->boolean('allow_ivideoapprove')->default(false)->change();
        });
    }
}
