<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksVideoUploadsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
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
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_video_uploads');
    }
}
