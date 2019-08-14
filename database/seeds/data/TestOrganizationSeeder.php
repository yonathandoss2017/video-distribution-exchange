<?php

use App\Models\Organization;
use Illuminate\Database\Seeder;

class TestOrganizationSeeder extends Seeder
{
    const ID_ORGANIZATION_1 = 1000001;
    const ID_ORGANIZATION_2 = 1000002;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $organizationSuperAdmin = factory(Organization::class)->states('superadmin')->create();
        $organizationSuperAdmin->id = Organization::ID_FOR_SUPER_ADMIN;
        $organizationSuperAdmin->save();

        $organization1 = factory(Organization::class)->create();
        $organization1->id = self::ID_ORGANIZATION_1;
        $organization1->save();

        $organization2 = factory(Organization::class)->create();
        $organization2->id = self::ID_ORGANIZATION_2;
        $organization2->save();
    }
}
