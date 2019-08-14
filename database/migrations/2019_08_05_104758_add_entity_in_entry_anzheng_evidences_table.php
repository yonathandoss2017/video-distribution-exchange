<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntityInEntryAnzhengEvidencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry_anzheng_evidences', function (Blueprint $table) {
            $table->string('entity')->nullable()->default('--')->after('entry_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry_anzheng_evidences', function (Blueprint $table) {
            $table->dropColumn('entity');
        });
    }
}
