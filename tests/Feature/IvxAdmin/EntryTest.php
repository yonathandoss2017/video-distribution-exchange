<?php

namespace Tests\Feature\IvxAdmin;

use Tests\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EntryTest extends BaseTestCase
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
    public function superadmin_can_visit()
    {
        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.entry.index'))
            ->assertStatus(200)
            ->assertSee('<h3 class="title">'.__('admin/entry.entries').'</h3>');
    }

    /**
     * @test
     */
    public function orgadmin_cannot_visit()
    {
        $this->actingAs($this->org_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.entry.index'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function property_manager_cannot_visit()
    {
        $this->actingAs($this->cp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.entry.index'))
            ->assertStatus(403);
    }
}
