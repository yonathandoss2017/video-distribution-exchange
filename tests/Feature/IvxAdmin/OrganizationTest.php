<?php

namespace Tests\Feature\IvxAdmin;

use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Support\Facades\Bus;
use App\Jobs\OrganizationEntryDeletionJob;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    private function store_organization_datas($type, $data = [])
    {
        $default = ['name' => 'test', 'country' => 'china'];
        $payload = array_merge($default, $data);

        $this->loginAsSuperAdmin();

        $response = null;
        if ('POST' == $type) {
            $response = $this->post(route('admin.organization.store'), $payload);
        } elseif ('PUT' == $type) {
            $response = $this->put(route('admin.organization.update', $this->organization->id), $payload);
        }

        return $response;
    }

    private function assert_delete_organization($entryNum, $organization)
    {
        $properties = factory(Property::class, 2)->create([
            'organization_id' => $organization->id,
        ]);
        $propertyIds = $properties->pluck('id')->toArray();
        factory(Entry::class, $entryNum)->create([
            'user_id' => $this->super_admin->id,
            'property_id' => array_random($propertyIds),
        ]);
        Bus::fake();
        $this->loginAsSuperAdmin();
        $response = $this->delete(route('admin.organization.destroy', $organization->id));
        if ($entryNum >= 50) {
            $response->assertSessionHas('success', __('admin/organization.deletion_is_in_processing'));
            $this->assertEquals(Organization::STATUS_DELETE_PROCESSING, $organization->fresh()->status);
            Bus::assertDispatched(OrganizationEntryDeletionJob::class);
        } else {
            $response->assertSessionHas('success', __('admin/organization.deleted_organization'));
            $this->assertSoftDeleted('organizations', $organization->refresh()->toArray());
            Bus::assertNotDispatched(OrganizationEntryDeletionJob::class);
        }
        $response->isRedirect(route('admin.organization.index'));
        $this->followRedirects($response)->assertStatus(200);
    }

    private function assert_access_page($user, $status)
    {
        $this->actingAs($user)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.organization.index'))
            ->assertStatus($status);

        $this->get(route('admin.organization.create'))
            ->assertStatus($status);

        $this->get(route('admin.organization.edit', $this->organization->id))
            ->assertStatus($status);
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access()
    {
        $this->get(route('admin.organization.index'))
            ->assertRedirect('login');

        $this->get(route('admin.organization.create'))
            ->assertRedirect('login');

        $this->get(route('admin.organization.edit', $this->organization->id))
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function superadmin_user_can_access()
    {
        $this->loginAsSuperAdmin();

        $this->get(route('admin.organization.index'))
            ->assertStatus(200)
            ->assertSee(route('admin.organization.edit', $this->organization->id));

        $this->get(route('admin.organization.create'))
            ->assertStatus(200)
            ->assertSee(route('admin.organization.store'));

        $this->get(route('admin.organization.edit', $this->organization->id))
            ->assertStatus(200)
            ->assertSee(htmlspecialchars($this->organization->name, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function not_superadmin_user_cannot_access()
    {
        $this->assert_access_page($this->org_admin, 403);
        $this->assert_access_page($this->cp_manager, 403);
        $this->assert_access_page($this->sp_manager, 403);
    }

    /**
     * @test
     */
    public function create_new_organization_success()
    {
        $response = $this->store_organization_datas('POST');
        $response->isRedirect(route('admin.organization.index'));
        $response->assertSessionHas('success', __('admin/organization.saved_organization'));
        $this->followRedirects($response)->assertStatus(200);
    }

    /**
     * @test
     */
    public function create_new_organization_missing_required_values_failed()
    {
        $response = $this->store_organization_datas('POST', ['name' => '', 'country' => '']);
        $response->assertSessionHasErrors(['name', 'country']);
    }

    /**
     * @test
     */
    public function edit_organization_success()
    {
        $response = $this->store_organization_datas('PUT');
        $response->isRedirect(route('admin.organization.index'));
        $response->assertSessionHas('success', __('admin/organization.updated_organization'));
        $this->followRedirects($response)->assertStatus(200);
    }

    /**
     * @test
     */
    public function edit_organization_missing_required_values_failed()
    {
        $response = $this->store_organization_datas('PUT', ['name' => '', 'country' => '']);
        $response->assertSessionHasErrors(['name', 'country']);
    }

    /**
     * @test
     */
    public function delete_organization_success()
    {
        $this->assert_delete_organization(1, factory(Organization::class)->create());
        $this->assert_delete_organization(50, factory(Organization::class)->create());
    }
}
