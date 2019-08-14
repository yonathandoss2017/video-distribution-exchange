<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldsOfDistributionRegionRightsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('distribution_region_rights', function (Blueprint $table) {
            $table->dropColumn(['platforms', 'supported_models', 'revenue_share_ex', 'revenue_share_nonex', 'license_fee_ex', 'license_fee_nonex', 'minimum_guarantee_ex', 'minimum_guarantee_nonex']);
        });
        Schema::table('distribution_region_rights', function (Blueprint $table) {
            $table->string('payment_mode')->nullable();
            $table->decimal('price', 10)->nullable();
            $table->unsignedInteger('update_count')->nullable();
            $table->decimal('revenue_share_cp')->nullable()->comment('in percentage');
            $table->decimal('revenue_share_sp')->nullable()->comment('in percentage');
            $table->text('payment_comments')->nullable();
            $table->string('api_share_to')->nullable();
            $table->string('download_resolution')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('distribution_region_rights', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'price', 'update_count', 'revenue_share_cp', 'revenue_share_sp', 'payment_comments', 'api_share_to', 'download_resolution']);
        });
        Schema::table('distribution_region_rights', function (Blueprint $table) {
            $table->string('platforms')->nullable();
            $table->string('supported_models')->nullable();
            $table->decimal('revenue_share_ex', 5)->nullable();
            $table->decimal('revenue_share_nonex', 5)->nullable();
            $table->decimal('license_fee_ex', 6)->nullable();
            $table->decimal('license_fee_nonex', 6)->nullable();
            $table->decimal('minimum_guarantee_ex', 6)->nullable();
            $table->decimal('minimum_guarantee_nonex', 6)->nullable();
        });
    }
}
