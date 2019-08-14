<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['property_id', 'user_id'], 'user_property_new_property_id_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
}
