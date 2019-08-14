<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceFieldIntoPlatformAlivodTranscodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('platform_alivod_transcodes', function (Blueprint $table) {
            $table->decimal('price', 9, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('platform_alivod_transcodes', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}
