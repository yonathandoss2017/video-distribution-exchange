<?php

namespace Tests\Unit\Models;

use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationTest extends BaseTestCase
{
    use RefreshDatabase;

    protected $orgSmall;                     //create 10 entries
    protected $orgLarge;                     //create 100 entries

    public function setUp()
    {
        parent::setUp();
        $this->seedBase();
        $this->loginAsSuperAdmin();
        $this->init_org_and_related_datas($this->orgSmall, 10);
        $this->init_org_and_related_datas($this->orgLarge, 100);
    }

    private function init_org_and_related_datas(&$organization, $entryNum)
    {
        $organization = factory(Organization::class)->create();

        $properties = factory(Property::class, 2)->create([
            'organization_id' => $organization->id,
        ]);

        $propertyIds = $properties->pluck('id')->toArray();

        for ($i = 0; $i < $entryNum; ++$i) {
            factory(Entry::class)->create([
                'user_id' => $this->super_admin->id,
                'property_id' => array_random($propertyIds),
            ]);
        }
    }

    /**
     * @test
     */
    public function test_delete()
    {
        $this->assert_delete_org_small_success();
        $this->assert_delete_org_large_success();
    }

    private function assert_delete_org_small_success()
    {
        $this->assertDatabaseHas('organizations', Organization::find($this->orgSmall->id)->toArray());

        $this->orgSmall->properties->map(function ($properties) {
            $this->assertDatabaseHas('properties', $properties->toArray());
        });

        $this->orgSmall->entries->map(function ($entries) {
            $this->assertDatabaseHas('entries', array_except($entries->toArray(), 'organization_id'));
        });

        $this->orgSmall->delete();

        $deletedOrganization = Organization::withTrashed()->find($this->orgSmall->id);

        $this->check_org_related_data_deleted($deletedOrganization);
    }

    private function assert_delete_org_large_success()
    {
        $this->assertDatabaseHas('organizations', Organization::find($this->orgLarge->id)->toArray());

        $this->orgLarge->properties->map(function ($properties) {
            $this->assertDatabaseHas('properties', $properties->toArray());
        });

        $this->orgLarge->entries->map(function ($entries) {
            $this->assertDatabaseHas('entries', array_except($entries->toArray(), 'organization_id'));
        });

        $this->orgLarge->delete();

        $deletedOrganization = Organization::withTrashed()->find($this->orgLarge->id);

        $this->check_org_related_data_deleted($deletedOrganization);
    }

    private function check_org_related_data_deleted($deletedOrganization)
    {
        $this->assertSoftDeleted('organizations', $deletedOrganization->toArray());

        $deletedOrganization->properties()->withTrashed()->get()->map(function ($properties) {
            $this->assertSoftDeleted('properties', $properties->toArray());
        });

        $deletedOrganization->entries()->withTrashed()->get()->map(function ($entries) {
            $this->assertSoftDeleted('entries', $entries->toArray());
        });
    }
}
