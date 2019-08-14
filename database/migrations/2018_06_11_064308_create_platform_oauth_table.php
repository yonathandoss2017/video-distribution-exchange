<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformOauthTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('platform_oauth', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform', 50);
            $table->string('api_key', 80)->nullable();
            $table->string('api_secret', 100)->nullable();
            $table->string('token', 200)->nullable();
            $table->string('refresh_token', 200)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('fb_page_token', 200)->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('organization_id')->unsigned()->nullable();
            $table->integer('property_id')->unsigned()->nullable();
            $table->text('display_name', 65535)->nullable();
            $table->unique(['platform', 'api_key', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('platform_oauth');
    }
}
