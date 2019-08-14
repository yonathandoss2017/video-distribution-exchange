<?php

use Illuminate\Database\Seeder;

class TestBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call(TestOrganizationSeeder::class);
        $this->call(TestRoleSeeder::class);
        $this->call(TestPropertySeeder::class);
        $this->call(TestUserSeeder::class);
    }
}
