<?php

use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use Illuminate\Database\Seeder;

class TestPropertySeeder extends Seeder
{
    const ID_CP_1 = 3000001;
    const ID_SP_1 = 3000002;
    const ID_CP_2 = 3000003;
    const ID_SP_2 = 3000004;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $propertySuperAdmin = factory(Property::class)->states('superadmin')->create();
        $propertySuperAdmin->id = Property::ID_FOR_ADMIN;
        $propertySuperAdmin->save();

        $cp1 = factory(PropertyCP::class)->create([
            'organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1,
            'name' => '8sian international',
        ]);
        $cp1->id = self::ID_CP_1;
        $cp1->save();

        $sp1 = factory(PropertySP::class)->create([
            'organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_1,
        ]);
        $sp1->id = self::ID_SP_1;
        $sp1->save();

        $cp2 = factory(PropertyCP::class)->create([
            'organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_2,
        ]);
        $cp2->id = self::ID_CP_2;
        $cp2->save();

        $sp2 = factory(PropertySP::class)->create([
            'organization_id' => TestOrganizationSeeder::ID_ORGANIZATION_2,
        ]);
        $sp2->id = self::ID_SP_2;
        $sp2->save();
    }
}
