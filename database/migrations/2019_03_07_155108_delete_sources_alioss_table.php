<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class DeleteSourcesAliossTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('sources_alioss');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
