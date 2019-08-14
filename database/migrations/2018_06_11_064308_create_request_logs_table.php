<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requester_user_id')->unsigned()->index('request_logs_requester_user_id_foreign');
            $table->string('subject');
            $table->text('message', 65535)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->dateTime('read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('request_logs');
    }
}
