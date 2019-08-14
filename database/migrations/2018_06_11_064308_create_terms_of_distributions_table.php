<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsOfDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('terms_of_distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_organization_id')->unsigned()->default(0)->index();
            $table->integer('cp_property_id')->unsigned()->index();
            $table->integer('sp_property_id')->unsigned()->nullable()->index();
            $table->string('status', 20)->nullable()->default('draft');
            $table->string('name');
            $table->string('internal_remarks')->nullable();
            $table->string('contract', 512)->nullable();
            $table->string('contract_name')->nullable();
            $table->timestamps();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('cp_deleted_at')->nullable();
            $table->dateTime('sp_deleted_at')->nullable();
            $table->integer('creator')->unsigned()->nullable()->index('terms_of_distributions_creator_foreign');
            $table->integer('updater')->unsigned()->nullable()->index('terms_of_distributions_updater_foreign');
            $table->boolean('show_new_mark')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('terms_of_distributions');
    }
}
