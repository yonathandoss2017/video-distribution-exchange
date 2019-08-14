<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropForeign('role_user_organization_id_foreign');
            $table->dropForeign('role_user_property_id_foreign');
            $table->dropForeign('role_user_role_id_foreign');
            $table->dropForeign('role_user_user_id_foreign');
        });
    }
}
