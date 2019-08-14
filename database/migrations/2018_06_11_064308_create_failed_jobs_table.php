<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('connection', 65535);
            $table->text('queue', 65535);
            $table->text('payload', 65535);
            $table->text('exception', 65535);
            $table->timestamp('failed_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('failed_jobs');
    }
}
