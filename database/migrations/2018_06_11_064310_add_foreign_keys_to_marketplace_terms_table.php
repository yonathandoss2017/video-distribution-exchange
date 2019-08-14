<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMarketplaceTermsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('marketplace_terms', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('marketplace_terms', function (Blueprint $table) {
            $table->dropForeign('marketplace_terms_created_by_foreign');
            $table->dropForeign('marketplace_terms_updated_by_foreign');
        });
    }
}
