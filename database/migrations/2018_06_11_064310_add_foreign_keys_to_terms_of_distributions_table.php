<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTermsOfDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('terms_of_distributions', function (Blueprint $table) {
            $table->foreign('cp_organization_id')->references('id')->on('organizations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('cp_property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('creator')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('sp_property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('updater')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('terms_of_distributions', function (Blueprint $table) {
            $table->dropForeign('terms_of_distributions_cp_organization_id_foreign');
            $table->dropForeign('terms_of_distributions_cp_property_id_foreign');
            $table->dropForeign('terms_of_distributions_creator_foreign');
            $table->dropForeign('terms_of_distributions_sp_property_id_foreign');
            $table->dropForeign('terms_of_distributions_updater_foreign');
        });
    }
}
