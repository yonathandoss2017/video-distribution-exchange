<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogPropertyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('request_log_property', function (Blueprint $table) {
            $table->integer('property_id')->unsigned()->index('request_log_property_property_id_foreign');
            $table->integer('request_log_id')->unsigned()->index('request_log_property_request_log_id_foreign');
            $table->string('property_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('request_log_property');
    }
}
