<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 50)->nullable();
            $table->integer('organization_id')->unsigned()->index();
            $table->string('name');
            $table->enum('type', ['sp_plus', 'sp', 'cp']);
            $table->timestamps();
            $table->string('api_key', 40)->nullable();
            $table->string('api_token', 40)->nullable();
            $table->softDeletes();
            $table->string('cdn_url', 512)->nullable();
            $table->string('start_steps', 50)->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('allow_livestream')->nullable()->default(0);
            $table->boolean('allow_ivideomobile')->nullable()->default(0);
            $table->boolean('allow_marketplace')->default(0);
            $table->boolean('retry_count')->default(0);
            $table->string('vast_ad_tag', 1000)->nullable();
            $table->string('mobile_vast_ad_tag', 1000)->nullable();
            $table->integer('ad_display_freq')->unsigned()->default(100);
            $table->string('ad_autoplay', 20)->default('auto');
            $table->integer('sp_max_bitrate')->nullable();
            $table->string('sp_referrer_filter')->nullable();
            $table->string('sp_ua_filter')->nullable();
            $table->string('sp_watermark_image')->nullable();
            $table->string('sp_watermark_destination')->nullable();
            $table->string('sp_banner_image')->nullable();
            $table->string('sp_banner_destination')->nullable();
            $table->string('sp_loader_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('properties');
    }
}
