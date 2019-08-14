<?php

namespace Tests\Feature\IvxAdmin;

use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\Organization;
use Illuminate\Support\Facades\Bus;
use App\Jobs\PropertyEntryDeletionJob;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentProvidersTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    private function assert_access_page($user, $status)
    {
        $this->actingAs($user)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.cp.index'))
            ->assertStatus($status);

        $this->get(route('admin.cp.edit', $this->organization->id))
            ->assertStatus($status);

        $this->get(route('admin.cp.api', $this->organization->id))
            ->assertStatus($status);
    }

    private function assert_delete_property($entryNum, $property)
    {
        factory(Entry::class, $entryNum)->create([
            'user_id' => $this->super_admin->id,
            'property_id' => $property->id,
        ]);
        Bus::fake();
        $this->loginAsSuperAdmin();
        $response = $this->delete(route('admin.cp.destroy', $property->id));
        if ($entryNum >= 50) {
            $response->assertSessionHas('success', __('admin/content_provider.property_delete_in_processing'));
            $this->assertEquals(Property::STATUS_DELETE_PROCESSING, $property->fresh()->status);
            Bus::assertDispatched(PropertyEntryDeletionJob::class);
        } else {
            $response->assertSessionHas('success', __('admin/content_provider.property_deleted_successfully'));
            $this->assertSoftDeleted('properties', $property->fresh()->toArray());
            Bus::assertNotDispatched(PropertyEntryDeletionJob::class);
        }
        $response->isRedirect(route('admin.cp.index'));
        $this->followRedirects($response)->assertStatus(200);
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access()
    {
        $this->get(route('admin.cp.index'))
            ->assertRedirect('login');

        $this->get(route('admin.cp.edit', $this->cp->id))
            ->assertRedirect('login');

        $this->get(route('admin.cp.api', $this->cp->id))
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function superadmin_user_can_access()
    {
        $this->loginAsSuperAdmin();

        $this->get(route('admin.cp.index'))
            ->assertStatus(200)
            ->assertSee('<h3 class="title">'.__('admin/sidebar.content_providers').'</h3>')
            ->assertSee(route('admin.cp.edit', $this->cp->id));

        $this->get(route('admin.cp.edit', $this->cp->id))
            ->assertStatus(200)
            ->assertSee('<h5>'.__('admin/content_provider.cp_information').'</h5>')
            ->assertSee($this->cp->name);

        $this->get(route('admin.cp.api', $this->cp->id))
            ->assertStatus(200)
            ->assertSee(__('admin/content_provider.no_api_available_for_this_cp'));
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
    public function edit_cp_success()
    {
        $organization_id = factory(Organization::class)->create()->id;

        $this->loginAsSuperAdmin();
        $response = $this->put(route('admin.cp.update', $this->cp->id), [
            'organization_id' => $organization_id,
        ]);

        $response->isRedirect(route('admin.cp.index'));
        $response->assertSessionHas('success', __('admin/content_provider.updated_property'));
        $this->followRedirects($response)->assertStatus(200);
        $this->assertEquals($organization_id, $this->cp->fresh()->organization_id);
    }

    /**
     * @test
     */
    public function delete_cp_success()
    {
        $this->assert_delete_property(1, factory(PropertyCP::class)->create());
        $this->assert_delete_property(50, factory(PropertyCP::class)->create());
    }
}
