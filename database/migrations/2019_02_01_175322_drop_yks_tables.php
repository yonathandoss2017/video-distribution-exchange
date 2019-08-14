<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropYksTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::drop('entry_feed');
        Schema::drop('yks_video_feeds');
        Schema::drop('yks_admins');
        Schema::drop('yks_channels');
        Schema::drop('yks_languages');
        Schema::drop('yks_playlists');
        Schema::drop('yks_proxies');
        Schema::drop('yks_subscriptions');
        Schema::drop('yks_third_parties');
        Schema::drop('yks_time_feed_items');
        Schema::drop('yks_time_feeds');
        Schema::drop('yks_videos');
        Schema::drop('yks_video_uploads');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::create('yks_video_uploads', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->bigInteger('video_id')->unsigned();
            $table->integer('subscription_id')->unsigned();
            $table->boolean('download_retries')->default(0);
            $table->boolean('upload_retries')->default(0);
            $table->boolean('status')->default(2);
            $table->boolean('notified')->default(0);
            $table->timestamps();
            $table->text('extra_data', 65535)->nullable();
            $table->integer('uploadable_id')->unsigned()->nullable();
            $table->string('uploadable_type')->nullable();
            $table->boolean('is_new')->default(0);
            $table->softDeletes();
            $table->string('proxies', 512)->nullable();
            $table->integer('entry_id')->unsigned()->nullable();
            $table->unique(['video_id', 'subscription_id'], 'yks_videos_upload_video_id_subscription_id_unique');
        });

        Schema::create('yks_videos', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('uid', 50)->unique();
            $table->integer('channel_id')->unsigned()->nullable();
            $table->string('title', 100)->nullable();
            $table->text('description', 65535)->nullable();
            $table->text('tags', 65535)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('notified')->default(0);
            $table->timestamps();
            $table->text('captions', 65535)->nullable();
            $table->string('def_lang', 30)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('thumbnail_width')->unsigned()->default(0);
            $table->string('thumbnail_url')->nullable();
            $table->string('url')->nullable();
        });

        Schema::create('yks_time_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('playlist_id')->unsigned()->index('yks_time_feeds_playlist_id_foreign');
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('property_id')->unsigned()->nullable()->index('yks_time_feeds_property_id_foreign');
            $table->smallInteger('type')->unsigned()->nullable();
            $table->foreign('playlist_id')->references('id')->on('playlists')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        Schema::create('yks_time_feed_items', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('guid');
            $table->integer('feed_id')->unsigned()->index('yks_time_feed_items_feed_id_foreign');
            $table->integer('entry_id')->unsigned()->nullable()->index('yks_time_feed_items_entry_id_foreign');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['guid', 'feed_id']);
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('feed_id')->references('id')->on('yks_time_feeds')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });

        Schema::create('yks_third_parties', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->integer('property_id')->unsigned()->index();
            $table->string('source', 10);
            $table->string('video_url', 512);
            $table->string('thumbnail_url', 512)->nullable();
            $table->text('captions', 65535)->nullable();
            $table->integer('uploadable_id')->unsigned()->nullable();
            $table->string('uploadable_type', 15)->nullable();
            $table->boolean('status')->default(3);
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->boolean('is_new')->default(1);
            $table->string('callback_url', 512)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->integer('entry_id')->unsigned()->nullable()->index('yks_third_parties_entry_id_foreign');
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('property_id')->references('id')->on('properties')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });

        Schema::create('yks_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id')->unsigned();
            $table->timestamps();
            $table->integer('playlist_id')->unsigned()->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->integer('feed_id')->unsigned()->nullable();
        });

        Schema::create('yks_proxies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('server_name')->unique();
            $table->string('country', 16)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('provider', 20)->default('nord');
            $table->boolean('enabled')->default(1);
        });

        Schema::create('yks_playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('uid', 50)->unique();
            $table->timestamps();
        });

        Schema::create('yks_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('yt_code', 30)->nullable();
            $table->string('yt_name', 100)->nullable();
            $table->string('dm_code', 30)->nullable();
            $table->string('dm_name', 100)->nullable();
            $table->string('ivs_code', 30)->nullable();
            $table->string('ivs_name', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('yks_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('uid', 50)->unique();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

        Schema::create('yks_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->timestamps();
            $table->boolean('yks_error')->default(0);
            $table->boolean('notify_syndication')->default(0);
        });

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

        Schema::create('entry_feed', function (Blueprint $table) {
            $table->integer('entry_id')->unsigned();
            $table->integer('feed_id')->unsigned()->index('entry_feed_feed_id_foreign');
            $table->primary(['entry_id', 'feed_id']);
            $table->foreign('entry_id')->references('id')->on('entries')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('feed_id')->references('id')->on('yks_video_feeds')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }
}
