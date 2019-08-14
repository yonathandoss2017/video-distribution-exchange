<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToLicenseNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('license_notifications', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('license_notifications', function (Blueprint $table) {
            $table->dropForeign('license_notifications_property_id_foreign');
            $table->dropForeign('license_notifications_user_id_foreign');
        });
    }
}
