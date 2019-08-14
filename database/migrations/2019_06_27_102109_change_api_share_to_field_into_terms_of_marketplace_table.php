<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeApiShareToFieldIntoTermsOfMarketplaceTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('terms_of_marketplace', function (Blueprint $table) {
            $table->string('api_share_to')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
