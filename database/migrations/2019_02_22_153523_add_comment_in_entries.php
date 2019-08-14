<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentInEntries extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->string('comment')->default('')->nullable()->after('platforms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
}
