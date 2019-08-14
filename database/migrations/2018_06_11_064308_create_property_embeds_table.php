<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyEmbedsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('property_embeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned()->unique();
            $table->string('site_url');
            $table->string('static_page_url');
            $table->timestamps();
            $table->string('related_video')->default('none');
            $table->string('vast_ad_tag', 1000)->nullable();
            $table->string('mobile_vast_ad_tag', 1000)->nullable();
            $table->string('show_video_in')->default('new-page');
            $table->boolean('allow_sharing')->default(0);
            $table->string('language', 10)->default('en');
            $table->boolean('show_prev_next_videos')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('property_embeds');
    }
}
