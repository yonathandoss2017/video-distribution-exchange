<?php

use App\Models\User;

class UsersTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->seedUsers();
    }

    public function seedUsers()
    {
        factory(User::class)->create([
            'name' => 'Admin',
            'email' => 'admin@svc.com',
            'password' => Hash::make('ABCabc01'),
        ]);
    }
}
