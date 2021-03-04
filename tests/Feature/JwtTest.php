<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JwtTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_can_get_token()
    {
        $password = $this->faker->password();

        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }
}
