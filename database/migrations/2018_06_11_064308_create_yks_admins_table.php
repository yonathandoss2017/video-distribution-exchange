<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksAdminsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->timestamps();
            $table->boolean('yks_error')->default(0);
            $table->boolean('notify_syndication')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_admins');
    }
}
