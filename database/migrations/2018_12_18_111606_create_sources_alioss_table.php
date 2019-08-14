<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourcesAliossTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sources_alioss', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned();
            $table->integer('platform_oauth_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('bucket', 50);
            $table->string('import_type', 15);
            $table->integer('import_recent_count')->unsigned()->nullable()->default(0);
            $table->boolean('distribute_new_videos')->default(0);
            $table->integer('playlist_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('sources_alioss');
    }
}
