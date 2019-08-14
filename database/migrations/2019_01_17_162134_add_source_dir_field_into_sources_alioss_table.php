<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceDirFieldIntoSourcesAliossTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sources_alioss', function (Blueprint $table) {
            $table->string('source_dir', 100)->nullable()->charset('utf8mb4')->collation('utf8mb4_bin')->after('bucket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sources_alioss', function (Blueprint $table) {
            $table->dropColumn('source_dir');
        });
    }
}
