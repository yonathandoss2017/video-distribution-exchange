<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    const ID_USER_SUPER_ADMIN = 1;
    const ID_USER_ADMIN_1 = 2;
    const ID_USER_CP_1 = 3;
    const ID_USER_SP_1 = 4;
    const ID_USER_ADMIN_2 = 5;
    const ID_USER_CP_2 = 6;
    const ID_USER_SP_2 = 7;
    const ID_USER_CONTENT_UPLOADER = 8;
    const ID_USER_CENSOR = 9;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $superAdmin = factory(User::class)->create();
        $superAdmin->id = self::ID_USER_SUPER_ADMIN;
        $superAdmin->save();
        $superAdmin->roles()->attach(TestRoleSeeder::ID_ROLE_SUPER_ADMIN, ['organization_id' => Organization::ID_FOR_SUPER_ADMIN, 'property_id' => Property::ID_FOR_ADMIN]);

        $admin1 = factory(User::class)->create();
        $admin1->id = self::ID_USER_ADMIN_1;
        $admin1->save();
        $admin1->roles()->attach(TestRoleSeeder::ID_ROLE_ADMIN, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1, 'property_id' => Property::ID_FOR_ADMIN]);

        $cpManager1 = factory(User::class)->create();
        $cpManager1->id = self::ID_USER_CP_1;
        $cpManager1->save();
        $cpManager1->roles()->attach(TestRoleSeeder::ID_ROLE_MANAGER, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1, 'property_id' => TestPropertySeeder::ID_CP_1]);

        $spManager1 = factory(User::class)->create();
        $spManager1->id = self::ID_USER_SP_1;
        $spManager1->save();
        $spManager1->roles()->attach(TestRoleSeeder::ID_ROLE_MANAGER, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1, 'property_id' => TestPropertySeeder::ID_SP_1]);

        $admin2 = factory(User::class)->create();
        $admin2->id = self::ID_USER_ADMIN_2;
        $admin2->save();
        $admin2->roles()->attach(TestRoleSeeder::ID_ROLE_ADMIN, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_2, 'property_id' => Property::ID_FOR_ADMIN]);

        $cpManager2 = factory(User::class)->create();
        $cpManager2->id = self::ID_USER_CP_2;
        $cpManager2->save();
        $cpManager2->roles()->attach(TestRoleSeeder::ID_ROLE_MANAGER, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_2, 'property_id' => TestPropertySeeder::ID_CP_2]);

        $spManager2 = factory(User::class)->create();
        $spManager2->id = self::ID_USER_SP_2;
        $spManager2->save();
        $spManager2->roles()->attach(TestRoleSeeder::ID_ROLE_MANAGER, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_2, 'property_id' => TestPropertySeeder::ID_SP_2]);

        $contentUploader = factory(User::class)->create();
        $contentUploader->id = self::ID_USER_CONTENT_UPLOADER;
        $contentUploader->save();
        $contentUploader->roles()->attach(TestRoleSeeder::ID_ROLE_UPLOADER, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1, 'property_id' => TestPropertySeeder::ID_CP_1]);

        $censor = factory(User::class)->create();
        $censor->id = self::ID_USER_CENSOR;
        $censor->save();
        $censor->roles()->attach(TestRoleSeeder::ID_ROLE_CENSOR, ['organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1, 'property_id' => TestPropertySeeder::ID_CP_1]);
    }
}
