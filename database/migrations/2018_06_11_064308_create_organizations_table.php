<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->string('address')->nullable();
            $table->string('country', 16)->nullable();
            $table->boolean('allow_ivideomobile')->default(0);
            $table->boolean('allow_livestream')->default(0);
            $table->boolean('allow_marketplace')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('retry_count')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('organizations');
    }
}
