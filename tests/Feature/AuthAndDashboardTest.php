<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'admin12345',
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpass123',
            'device_name' => 'phpunit',
        ])->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Invalid credentials.');

        $this->postJson('/api/login', [
            'email' => 'notfound@example.com',
            'password' => 'wrongpass123',
            'device_name' => 'phpunit',
        ])->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Invalid credentials.');
    }

    public function test_user_can_login_and_access_protected_dashboard_route(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
            'device_name' => 'phpunit',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'user',
                'token',
                'token_type',
                'data' => ['user', 'token', 'token_type'],
            ]);

        $token = $loginResponse->json('data.token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/dashboard/summary')
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertNotNull($user->fresh());
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->getJson('/api/dashboard/summary')
            ->assertStatus(401);
    }

    public function test_frontend_dashboard_endpoint_returns_expected_keys(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure([
                'stats',
                'enrollmentTrend',
                'programDistribution',
                'attendancePatterns',
                'capacityData',
                'recentEnrollments',
                'activities',
            ]);
    }
}
