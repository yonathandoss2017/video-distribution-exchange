<?php

namespace Tests\Feature\Manage;

use Faker\Factory;
use Tests\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends BaseTestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access()
    {
        $this->get(route('manage.property.select'))->assertRedirect('login');
    }

    /**
     * @test
     */
    public function superadmin_or_orgadmin_user_can_see_all_properties()
    {
        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.select'))
            ->assertStatus(200)
            ->assertSee('<small class="label cp">CP</small>')
            ->assertSee('<small class="label sp">SP</small>');

        $this->actingAs($this->org_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.select'))
            ->assertStatus(200)
            ->assertSee('<small class="label cp">CP</small>')
            ->assertSee('<small class="label sp">SP</small>');
    }

    /**
     * @test
     */
    public function property_user_only_can_see_the_own_properties()
    {
        $this->actingAs($this->cp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.select'))
            ->assertStatus(200)
            ->assertSee('<small class="label cp">CP</small>')
            ->assertDontSee('<small class="label sp">SP</small>');

        $this->actingAs($this->sp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.select'))
            ->assertStatus(200)
            ->assertSee('<small class="label sp">SP</small>')
            ->assertDontSee('<small class="label cp">CP</small>');
    }

    /**
     * @test
     */
    public function superadmin_or_orgadmin_user_can_access_create_property_page()
    {
        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.add'))
            ->assertStatus(200)
            ->assertSee('<div class="title">'.__('manage/organization/property.new_property').'</div>');

        $this->actingAs($this->org_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.add'))
            ->assertStatus(200)
            ->assertSee('<div class="title">'.__('manage/organization/property.new_property').'</div>');
    }

    /**
     * @test
     */
    public function property_user_cannot_access_create_property_page()
    {
        $this->actingAs($this->cp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.add'))
            ->assertStatus(403);

        $this->actingAs($this->sp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('manage.property.add'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function create_new_property_success()
    {
        $faker = Factory::create();
        $name = $faker->name;
        $arr = ['cp', 'sp'];
        $type = $arr[array_rand($arr)];
        $url = route('manage.property.store');
        $redirectUrl = route('manage.property.select');

        $this
            ->actingAs($this->org_admin)
            ->withSession(['organization' => $this->organization->id])
            ->post($url, [
                'property_name' => $name,
                'property_type' => $type,
            ])
            ->assertSessionHas('success')
            ->assertRedirect($redirectUrl);

        $this->assertDatabaseHas('properties', [
            'name' => $name,
            'type' => $type,
        ]);
    }

    /**
     * @test
     */
    public function create_new_property_missing_required_values_failed()
    {
        $arr = ['cp', 'sp'];
        $type = $arr[array_rand($arr)];
        $url = route('manage.property.store');
        $redirectUrl = route('manage.property.add');

        $this->get(route('manage.property.add'));
        $this
            ->actingAs($this->org_admin)
            ->withSession(['organization' => $this->organization->id])
            ->post($url, [
                'property_type' => $type,
            ])
            ->assertSessionHasErrors([
                'property_name',
            ])
            ->assertRedirect($redirectUrl);
    }
}
