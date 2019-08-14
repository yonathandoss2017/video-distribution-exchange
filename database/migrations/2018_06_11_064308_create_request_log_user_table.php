<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('request_log_user', function (Blueprint $table) {
            $table->integer('request_log_id')->unsigned()->index('request_log_user_request_log_id_foreign');
            $table->integer('user_id')->unsigned()->index('request_log_user_user_id_foreign');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('request_log_user');
    }
}
