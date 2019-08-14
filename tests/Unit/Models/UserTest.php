<?php

namespace Tests\Unit\Models;

use Faker\Factory;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_delete()
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

        $this->assertTrue($this->user->delete());

        $this->assertDatabaseMissing('users', [
            'remarks' => $email,
            'deleted_at' => null,
        ]);
    }
}
