<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYksProxiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('yks_proxies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('server_name')->unique();
            $table->string('country', 16)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('provider', 20)->default('nord');
            $table->boolean('enabled')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('yks_proxies');
    }
}
