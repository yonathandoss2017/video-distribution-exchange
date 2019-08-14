<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToDistributionRegionRightsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('distribution_region_rights', function (Blueprint $table) {
            $table->foreign('tod_id')->references('id')->on('terms_of_distributions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('distribution_region_rights', function (Blueprint $table) {
            $table->dropForeign('distribution_region_rights_tod_id_foreign');
        });
    }
}
