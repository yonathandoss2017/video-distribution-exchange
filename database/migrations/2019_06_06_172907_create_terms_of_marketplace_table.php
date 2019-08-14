<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsOfMarketplaceTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('terms_of_marketplace', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('name');
            $table->string('region_allowed');
            $table->string('region_excepted')->nullable();
            $table->string('payment_mode');
            $table->decimal('price', 10)->nullable();
            $table->unsignedInteger('update_count')->nullable();
            $table->string('exclusivity', 20)->nullable();
            $table->decimal('revenue_share_cp')->nullable()->comment('in percentage');
            $table->decimal('revenue_share_sp')->nullable()->comment('in percentage');
            $table->string('payment_comments', 500)->nullable();
            $table->string('api_share_to');
            $table->string('download_resolution')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('terms_of_marketplace');
    }
}
