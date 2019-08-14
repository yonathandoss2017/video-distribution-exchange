<?php

use Illuminate\Database\Migrations\Migration;

class ModifyDescriptionInProperties extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('ALTER TABLE properties MODIFY description TEXT after type');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE properties MODIFY description VARCHAR(255) default \'\' after type');
    }
}
