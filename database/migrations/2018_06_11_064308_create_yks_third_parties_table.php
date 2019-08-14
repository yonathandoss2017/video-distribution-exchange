<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksThirdPartiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_third_parties');
    }
}
