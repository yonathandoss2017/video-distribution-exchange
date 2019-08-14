<?php

namespace Tests\Feature\Manage;

use Faker\Factory;
use TestRoleSeeder;
use App\Models\User;
use Tests\BaseTestCase;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\Organization;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\UserActivation\UserActivationWithPassResetEmail;

class UsersTest extends BaseTestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
        $this->init_user_info();
    }

    private function init_user_info()
    {
        $this->user = \factory(User::class)->create(['activated_at' => null]);
        $this->user->roles()->attach(TestRoleSeeder::ID_ROLE_MANAGER, ['organization_id' => $this->organization->id, 'property_id' => $this->cp->id]);
    }

    private function store_user_datas($type, $data = [])
    {
        $payload = array_merge([], $data);

        $this->loginAsSuperAdmin();

        $response = null;
        if ('POST' == $type) {
            $response = $this->post(route('manage.user.store'), $payload);
        } elseif ('PATCH' == $type) {
            $response = $this->patch(route('manage.user.update', $this->user->id), $payload);
        }

        return $response;
    }

    private function assert_access_page($user, $status)
    {
        $this->actingAs($user)
            ->withSession(['organization' => $this->organization->id]);

        $response = $this->get(route('manage.users'))
            ->assertStatus($status);
        if (200 == $status) {
            $response->assertSee(route('manage.user.edit', $this->user->id));
        } else {
            $response->assertDontSee(route('manage.user.edit', $this->user->id));
        }

        $response = $this->get(route('manage.user.add'))
            ->assertStatus($status);
        if (200 == $status) {
            $response->assertSee(route('manage.user.store'));
        } else {
            $response->assertDontSee(route('manage.user.store'));
        }

        $response = $this->get(route('manage.user.edit', $this->user->id))
            ->assertStatus($status);
        if (200 == $status) {
            $response->assertSee(htmlspecialchars($this->user->name, ENT_QUOTES));
        } else {
            $response->assertDontSee(htmlspecialchars($this->user->name, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access()
    {
        $this->get(route('manage.users'))
            ->assertRedirect('login');

        $this->get(route('manage.user.add'))
            ->assertRedirect('login');

        $this->get(route('manage.user.edit', $this->org_admin->id))
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function superadmin_or_orgadmin_user_can_access()
    {
        $this->assert_access_page($this->super_admin, 200);
        $this->assert_access_page($this->org_admin, 200);
    }

    /**
     * @test
     */
    public function property_user_cannot_access()
    {
        $this->assert_access_page($this->cp_manager, 403);
        $this->assert_access_page($this->sp_manager, 403);
    }

    /**
     * @test
     */
    public function create_new_user_success()
    {
        Notification::fake();
        $faker = Factory::create();
        $post = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            $this->cp->id.'-role' => TestRoleSeeder::ID_ROLE_MANAGER,
            $this->sp->id.'-role' => TestRoleSeeder::ID_ROLE_MANAGER,
        ];
        $response = $this->store_user_datas('POST', $post);
        $response->isRedirect(route('manage.users'));
        $this->followRedirects($response)->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => $post['name'],
            'email' => $post['email'],
        ]);
        $user = User::where('name', $post['name'])->first();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => TestRoleSeeder::ID_ROLE_MANAGER,
            'property_id' => $this->cp->id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => TestRoleSeeder::ID_ROLE_MANAGER,
            'property_id' => $this->sp->id,
        ]);
        Notification::assertSentTo($user, UserActivationWithPassResetEmail::class, function ($email) use ($user) {
            return $email->user->id == $user->id;
        });
    }

    /**
     * @test
     */
    public function create_new_user_missing_required_values_failed()
    {
        Notification::fake();
        $response = $this->store_user_datas('POST');
        $response->assertSessionHasErrors(['name', 'email']);
        Notification::assertNotSentTo($this->user, UserActivationWithPassResetEmail::class);
    }

    /**
     * @test
     */
    public function create_new_user_with_exist_user_failed()
    {
        Notification::fake();
        $post = [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ];
        $response = $this->store_user_datas('POST', $post);
        $response->assertSessionHas('error', __('manage/organization/user.user_already_exists'));
        Notification::assertNotSentTo($this->user, UserActivationWithPassResetEmail::class);
    }

    /**
     * @test
     */
    public function create_new_user_with_wrong_access_level_failed()
    {
        Notification::fake();
        $faker = Factory::create();
        $post = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            '1-role' => TestRoleSeeder::ID_ROLE_MANAGER,
            '2-role' => TestRoleSeeder::ID_ROLE_MANAGER,
        ];
        $response = $this->store_user_datas('POST', $post);
        $response->assertSessionHas('error', __('manage/organization/user.missing_access_level'));
        Notification::assertNotSentTo($this->user, UserActivationWithPassResetEmail::class);
    }

    /**
     * @test
     */
    public function edit_user_success()
    {
        Notification::fake();
        $post = [
            $this->cp->id.'-role' => 0,
            $this->sp->id.'-role' => TestRoleSeeder::ID_ROLE_MANAGER,
        ];
        $response = $this->store_user_datas('PATCH', $post);
        $response->isRedirect(route('manage.users'));
        $this->followRedirects($response)->assertStatus(200);
        $this->assertDatabaseMissing('role_user', [
            'user_id' => $this->user->id,
            'role_id' => TestRoleSeeder::ID_ROLE_MANAGER,
            'property_id' => $this->cp->id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $this->user->id,
            'role_id' => TestRoleSeeder::ID_ROLE_MANAGER,
            'property_id' => $this->sp->id,
        ]);
        $user = $this->user;
        Notification::assertSentTo($user, UserActivationWithPassResetEmail::class, function ($email) use ($user) {
            return $email->user->id == $user->id;
        });
    }

    /**
     * @test
     */
    public function edit_user_with_wrong_access_level_failed()
    {
        Notification::fake();
        $post = [
            '1-role' => TestRoleSeeder::ID_ROLE_MANAGER,
            '2-role' => TestRoleSeeder::ID_ROLE_MANAGER,
        ];
        $response = $this->store_user_datas('PATCH', $post);
        $response->assertSessionHas('error', __('manage/organization/user.missing_access_level'));
        Notification::assertNotSentTo($this->user, UserActivationWithPassResetEmail::class);
    }

    /**
     * @test
     */
    public function cp_can_see_content_uploader_options_in_access_level()
    {
        $organization = \factory(Organization::class)->create();
        $property_cp = \factory(PropertyCP::class)->create(['organization_id' => $organization->id]);

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $organization->id])
            ->get(route('manage.user.add'))
            ->assertSee(htmlspecialchars($property_cp->name, ENT_QUOTES))
            ->assertSee('<option value="'.TestRoleSeeder::ID_ROLE_UPLOADER.'">'.__('common.content_uploader').'</option>');
    }

    /**
     * @test
     */
    public function sp_cannot_see_content_uploader_options_in_access_level()
    {
        $organization = \factory(Organization::class)->create();
        $property_sp = \factory(PropertySP::class)->create(['organization_id' => $organization->id]);

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $organization->id])
            ->get(route('manage.user.add'))
            ->assertSee(htmlspecialchars($property_sp->name, ENT_QUOTES))
            ->assertDontSee('<option value="'.TestRoleSeeder::ID_ROLE_UPLOADER.'">'.__('common.content_uploader').'</option>');
    }

    /**
     * @test
     */
    public function delete_user_success()
    {
        $this->loginAsSuperAdmin();
        $response = $this->delete(route('manage.user.destroy', $this->user->id));
        $response->assertSessionHas('success', __('manage/organization/user.user_success_deleted'));
        $this->assertDatabaseMissing('role_user', [
            'user_id' => $this->user->id,
            'role_id' => TestRoleSeeder::ID_ROLE_MANAGER,
            'organization_id' => $this->organization->id,
            'property_id' => $this->cp->id,
        ]);
    }
}
