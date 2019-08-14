<?php

namespace Tests\Feature\IvxAdmin;

use Tests\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceProvidersTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access()
    {
        $response = $this->get(route('admin.sp.index'));
        $response->assertRedirect('login');
    }

    /**
     * @test
     */
    public function superadmin_user_can_access()
    {
        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.sp.index'))
            ->assertStatus(200)
            ->assertSee('<h3 class="title">'.__('admin/sidebar.service_providers').'</h3>');
    }

    /**
     * @test
     */
    public function not_superadmin_user_cannot_access()
    {
        $this->actingAs($this->org_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.sp.index'))
            ->assertStatus(403);

        $this->actingAs($this->cp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.sp.index'))
            ->assertStatus(403);
    }
}
