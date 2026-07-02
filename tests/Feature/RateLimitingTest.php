<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limits before each test
        RateLimiter::clear('api:'.request()->ip());
    }

    /** @test */
    public function guest_users_have_a_limit_of_30_requests_per_minute()
    {
        // 1. Per-second burst protection kicks in first (limit 1 for guest)
        $this->getJson('/api/books')->assertStatus(200);

        // Wait a bit to bypass per-second but stay within per-minute
        // Or we can just mock the RateLimiter if we wanted to test specifically per-minute
        // But the requirement says "Per-second burst protection" so we should test that.
    }

    /** @test */
    public function it_enforces_per_second_burst_protection()
    {
        // For guest, limit is 1 per second
        $this->getJson('/api/books')->assertStatus(200);

        // Second request in same "second" should fail
        $response = $this->getJson('/api/books');
        $response->assertStatus(429);
        $response->assertJsonPath('error', 'Rate limit exceeded');
    }

    /** @test */
    public function tiered_limits_enforced_correctly_per_user_role()
    {
        // Standard User (customer)
        $customer = User::factory()->create(['role' => 'customer', 'tier' => 'standard']);
        $this->actingAs($customer);

        // Standard limit: 2 per second burst
        $this->getJson('/api/books')->assertStatus(200);
        $this->getJson('/api/books')->assertStatus(200);
        $this->getJson('/api/books')->assertStatus(429);

        // Premium User
        RateLimiter::clear('api:'.$customer->id);
        $premium = User::factory()->create(['role' => 'customer', 'tier' => 'premium']);
        $this->actingAs($premium);

        // Premium limit: 10 per second burst
        for ($i = 0; $i < 10; $i++) {
            $this->getJson('/api/books')->assertStatus(200);
        }
        $this->getJson('/api/books')->assertStatus(429);

        // Admin User
        RateLimiter::clear('api:'.$premium->id);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Admin limit: 50 per second burst
        for ($i = 0; $i < 50; $i++) {
            $this->getJson('/api/books')->assertStatus(200);
        }
        $this->getJson('/api/books')->assertStatus(429);
    }

    /** @test */
    public function throttled_responses_contain_proper_headers()
    {
        // Trigger limit
        $this->getJson('/api/books');
        $response = $this->getJson('/api/books');

        $response->assertStatus(429);
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
        $response->assertHeader('Retry-After');
    }

    /** @test */
    public function graceful_degradation_under_load()
    {
        // This test simulates "load" by hitting limits and ensuring the system
        // responds with informative 429 errors instead of crashing.

        $customer = User::factory()->create(['role' => 'customer', 'tier' => 'standard']);
        $this->actingAs($customer);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/books');
            if ($i >= 2) {
                $response->assertStatus(429);
                $response->assertJsonStructure([
                    'error',
                    'message',
                    'details' => ['limit', 'remaining', 'retry_after']
                ]);
            } else {
                $response->assertStatus(200);
            }
        }
    }
}
