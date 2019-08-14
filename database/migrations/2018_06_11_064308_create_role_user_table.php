<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned()->index('role_user_role_id_foreign');
            $table->integer('organization_id')->unsigned()->index('role_user_organization_id_foreign');
            $table->integer('property_id')->unsigned()->index('role_user_property_id_foreign');
            $table->timestamps();
            $table->primary(['user_id', 'role_id', 'organization_id', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('role_user');
    }
}
