<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AddDppUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::create([
            'name' => 'andrew',
            'email' => 'andrew@digital-vantage.com',
            'password' => Hash::make('ABCabc01'),
            'remarks' => '',
        ]);

        $roleId = Role::where('name', Role::DPP_ADMIN)->value('id');

        $user->roles()->attach($roleId, [
            'organization_id' => Organization::ID_FOR_SUPER_ADMIN,
            'property_id' => Property::ID_FOR_ADMIN,
        ]);
    }
}
