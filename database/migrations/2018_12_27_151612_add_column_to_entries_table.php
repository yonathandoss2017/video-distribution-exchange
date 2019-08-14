<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToEntriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->smallInteger('width')->nullable()->after('duration')->default(0);
            $table->smallInteger('height')->nullable()->after('width')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['width']);
            $table->dropColumn(['height']);
        });
    }
}
