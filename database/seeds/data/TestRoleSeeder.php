<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class TestRoleSeeder extends Seeder
{
    const ID_ROLE_SUPER_ADMIN = 1;
    const ID_ROLE_ADMIN = 2;
    const ID_ROLE_MANAGER = 3;
    const ID_ROLE_UPLOADER = 5;
    const ID_ROLE_CENSOR = 6;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roleSuperAdmin = Role::create(['name' => Role::SUPER_ADMIN, 'display_name' => 'Super Admin']);
        $roleSuperAdmin->id = self::ID_ROLE_SUPER_ADMIN;
        $roleSuperAdmin->save();

        $roleAdmin = Role::create(['name' => Role::ORGANIZATION_ADMIN, 'display_name' => 'Organization Admin']);
        $roleAdmin->id = self::ID_ROLE_ADMIN;
        $roleAdmin->save();

        $roleManager = Role::create(['name' => Role::PROPERTY_MANAGER, 'display_name' => 'Property Manager']);
        $roleManager->id = self::ID_ROLE_MANAGER;
        $roleManager->save();

        $roleManager = Role::create(['name' => Role::CONTENT_UPLOADER, 'display_name' => ' Content Uploader']);
        $roleManager->id = self::ID_ROLE_UPLOADER;
        $roleManager->save();

        $roleCensor = Role::create(['id' => self::ID_ROLE_CENSOR, 'name' => Role::CENSOR, 'display_name' => 'Censor']);
    }
}
