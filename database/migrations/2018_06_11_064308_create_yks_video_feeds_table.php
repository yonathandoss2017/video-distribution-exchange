<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksVideoFeedsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_video_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned();
            $table->integer('platform_oauth_id')->unsigned()->nullable();
            $table->string('source', 100);
            $table->string('source_name', 200)->nullable();
            $table->string('feedable_type', 15);
            $table->integer('feedable_id')->unsigned()->nullable();
            $table->string('source_id', 50);
            $table->string('source_api_key', 50);
            $table->boolean('distribute_new_videos')->default(0);
            $table->string('destination', 100)->nullable();
            $table->text('category', 65535)->nullable();
            $table->boolean('publish_immediately')->nullable()->default(0);
            $table->string('import_type', 15);
            $table->integer('import_recent_count')->unsigned()->nullable()->default(0);
            $table->string('status', 20)->default('Pending');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('share_on_fb_profile')->default(0);
            $table->boolean('share_on_fb_page')->default(0);
            $table->string('fb_page_id', 100)->nullable();
            $table->integer('fb_platform_oauth_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('playlist_id')->unsigned()->nullable();
            $table->string('dst_playlist_id', 50)->nullable();
            $table->boolean('privacy')->nullable();
            $table->string('only_lang', 100)->nullable();
            $table->boolean('block_ads')->nullable();
            $table->string('yt_channel_id', 80)->nullable();
            $table->string('yt_playlist_id', 80)->nullable();
            $table->string('proxies', 200)->default('sg');
            $table->string('transcoder', 20)->default('8034182');
            $table->unique(['property_id', 'source_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_video_feeds');
    }
}
