<?php

namespace Tests\Feature\IvxAdmin;

use Faker\Factory;
use TestUserSeeder;
use App\Models\User;
use Tests\BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends BaseTestCase
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
        $this->get(route('admin.user.index'))
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function superadmin_user_can_access()
    {
        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id]);

        $this->get(route('admin.user.index'))
            ->assertStatus(200)
            ->assertSee('<h3 class="title">'.__('admin/sidebar.users').'</h3>');
    }

    /**
     * @test
     */
    public function not_superadmin_user_can_not_access()
    {
        $org_admin = User::find(TestUserSeeder::ID_USER_ADMIN_1);
        $this->actingAs($org_admin)
            ->withSession(['organization' => $this->organization->id]);
        $this->get(route('admin.user.index'))
            ->assertStatus(403);

        $cp_manager = User::find(TestUserSeeder::ID_USER_CP_1);
        $this->actingAs($cp_manager)
            ->withSession(['organization' => $this->organization->id]);
        $this->get(route('admin.user.index'))
            ->assertStatus(403);

        $sp_manager = User::find(TestUserSeeder::ID_USER_SP_1);
        $this->actingAs($sp_manager)
            ->withSession(['organization' => $this->organization->id]);
        $this->get(route('admin.user.index'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function create_new_user_success()
    {
        $faker = Factory::create();
        $email = $faker->email;
        $name = $faker->name;
        $password = $faker->password;

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id])
            ->post(route('admin.user.store'), [
                'email' => $email,
                'name' => $name,
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('admin.user.index'));

        $this->assertDatabaseHas('users', [
            'name' => htmlspecialchars($name, ENT_QUOTES),
            'email' => $email,
        ]);
    }

    /**
     * @test
     */
    public function create_new_user_missing_required_values_filed()
    {
        $faker = Factory::create();
        $remarks = $faker->name;

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id])
            ->post(route('admin.user.store'), [
                'remarks' => $remarks,
            ])
            ->assertSessionHasErrors([
                'email', 'name', 'password', 'password_confirmation',
            ])
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', [
            'remarks' => $remarks,
        ]);
    }

    /**
     * @test
     */
    public function edit_user_success()
    {
        $faker = Factory::create();
        $email = $faker->email;
        $password = $faker->password;

        $this->user = factory(User::class)->create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make($password),
            'remarks' => $email,
        ]);

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id])
            ->patch(route('admin.user.update', $this->user->id), [
                'name' => 'name_test',
                'is_active' => 1,
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('admin.user.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'name_test',
            'email' => $email,
            'remarks' => $email,
        ]);
    }

    /**
     * @test
     */
    public function edit_user_missing_required_values_failed()
    {
        $faker = Factory::create();
        $email = $faker->email;
        $password = $faker->password;

        $this->user = factory(User::class)->create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make($password),
            'remarks' => $email,
        ]);

        $new_faker = Factory::create();
        $new_password = $new_faker->password;

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id])
            ->patch(route('admin.user.update', $this->user->id), [
                'password' => $new_password,
            ])
            ->assertSessionHasErrors([
                'is_active',
            ])
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'name' => $email,
            'email' => $email,
            'remarks' => $email,
        ]);
    }

    /**
     * @test
     */
    public function delete_user_success()
    {
        $faker = Factory::create();
        $email = $faker->email;
        $password = $faker->password;

        $this->user = factory(User::class)->create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make($password),
            'remarks' => $email,
        ]);

        $this->actingAs($this->super_admin)
            ->withSession(['organization' => $this->organization->id])
            ->delete(route('admin.user.destroy', $this->user->id))
            ->assertSessionHas('success')
            ->assertRedirect(route('admin.user.index'));
    }
}
