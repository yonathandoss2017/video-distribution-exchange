<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketplaceTermsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('marketplace_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->unique();
            $table->string('allowed_regions');
            $table->string('excepted_regions')->nullable();
            $table->string('platforms')->nullable();
            $table->string('exclusivity')->nullable();
            $table->string('supported_models')->nullable();
            $table->string('revenue_share');
            $table->string('license_fee');
            $table->string('minimun_guarantee');
            $table->string('footnote')->nullable();
            $table->integer('created_by')->unsigned()->index('marketplace_terms_created_by_foreign');
            $table->integer('updated_by')->unsigned()->index('marketplace_terms_updated_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('marketplace_terms');
    }
}
