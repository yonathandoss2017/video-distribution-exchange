<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('remarks');
            $table->string('longid', 180)->nullable();
            $table->dateTime('activated_at')->nullable();
            $table->boolean('skip_start')->default(0);
            $table->softDeletes();
            $table->string('role_user_backup')->nullable();
            $table->boolean('vewd_step')->default(0);
            $table->string('login_ip', 50)->nullable();
            $table->string('last_login_ip', 50)->nullable();
            $table->dateTime('last_active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('users');
    }
}
