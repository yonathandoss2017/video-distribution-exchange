<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistributionRegionRightsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('distribution_region_rights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tod_id')->unsigned()->index();
            $table->string('allowed_regions');
            $table->string('excepted_regions')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('platforms')->nullable();
            $table->string('exclusivity')->nullable();
            $table->string('supported_models')->nullable();
            $table->decimal('revenue_share_ex', 5)->nullable();
            $table->decimal('revenue_share_nonex', 5)->nullable();
            $table->decimal('license_fee_ex', 6)->nullable();
            $table->decimal('license_fee_nonex', 6)->nullable();
            $table->decimal('minimum_guarantee_ex', 6)->nullable();
            $table->decimal('minimum_guarantee_nonex', 6)->nullable();
            $table->string('extra_terms')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('distribution_region_rights');
    }
}
