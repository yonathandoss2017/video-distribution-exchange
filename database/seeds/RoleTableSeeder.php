<?php

use App\Models\Role;
use App\Models\User;

class RoleTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (0 == Role::where('name', 'super_admin')->count()) {
            Role::create(['name' => 'super_admin', 'display_name' => 'Super Admin']);
        }
        if (0 == Role::where('name', 'organization_admin')->count()) {
            Role::create(['name' => 'organization_admin', 'display_name' => 'Organization Admin']);
        }
        if (0 == Role::where('name', 'property_manager')->count()) {
            Role::create(['name' => 'property_manager', 'display_name' => 'Property Manager']);
        }
        if (0 == Role::where('name', 'dpp_admin')->count()) {
            Role::create(['name' => 'dpp_admin', 'display_name' => 'Digital Product Placement Admin']);
        }
        if (0 == Role::where('name', 'content_uploader')->count()) {
            Role::create(['name' => 'content_uploader', 'display_name' => 'Content Uploader']);
        }
        if (0 == Role::where('name', 'super_operator')->count()) {
            Role::create(['name' => 'super_operator', 'display_name' => 'Super Operator']);
        }
        if (0 == Role::where('name', 'censor')->count()) {
            Role::create(['name' => 'censor', 'display_name' => 'Censor']);
        }

        $superadmin = User::find(self::USER_ID_BY_ROLE['super_admin']);
        if (0 == ($superadmin->roles()->where([
            'user_id' => self::USER_ID_BY_ROLE['super_admin'],
            'role_id' => self::USER_ID_BY_ROLE['super_admin'],
            'organization_id' => self::ORG_ID_FOR_SUPER_ADMIN,
            'property_id' => self::PROPERTY_ID_FOR_ADMIN,
        ])->count())) {
            $superadmin->roles()->attach(self::USER_ID_BY_ROLE['super_admin'], [
                'organization_id' => self::ORG_ID_FOR_SUPER_ADMIN,
                'property_id' => self::PROPERTY_ID_FOR_ADMIN,
            ]);
        }
    }
}
