<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear the throttle hits between tests so the rate-limit test
        // isn't polluted by the other attempts in this file.
        RateLimiter::clear('api');
    }

    public function test_login_issues_a_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@admin.test',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@admin.test',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'email', 'name']])
            ->assertJsonPath('user.email', 'admin@admin.test');

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_rejects_bad_credentials_with_422(): void
    {
        User::factory()->create([
            'email' => 'admin@admin.test',
            'password' => Hash::make('password'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@admin.test',
            'password' => 'wrong',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_validates_required_fields(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_is_throttled_after_repeated_attempts(): void
    {
        // 6 requests per minute is the configured limit — the 7th must 429.
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', [
                'email' => 'noone@nowhere.test',
                'password' => 'whatever',
            ]);
        }

        $this->postJson('/api/login', [
            'email' => 'noone@nowhere.test',
            'password' => 'whatever',
        ])->assertStatus(429);
    }
}
