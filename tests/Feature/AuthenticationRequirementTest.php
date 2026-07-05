<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TwoFactorSecret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendOtpNotification;
use Tests\TestCase;

class AuthenticationRequirementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement: [ ] Secure Login
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'status' => 'active',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Requirement: [ ] Remember Me
     */
    public function test_login_can_be_remembered()
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'status' => 'active',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
            'remember' => 'on',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertCookie(\Illuminate\Support\Facades\Auth::getRecallerName());
    }

    /**
     * Requirement: [ ] Account Lockout
     * Temporary lock after 5 failed attempts
     */
    public function test_login_request_is_rate_limited_after_five_failed_attempts()
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many login attempts', session('errors')->get('email')[0]);
    }

    /**
     * Requirement: [ ] Multi-Factor Authentication (MFA)
     */
    public function test_mfa_is_required_if_enabled()
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'status' => 'active',
        ]);

        TwoFactorSecret::create([
            'user_id' => $user->id,
            'secret' => 'base32secret3232',
            'recovery_codes' => json_encode(['code1', 'code2']),
            'enabled' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertTrue(session('2fa_required'));

        Notification::assertSentTo($user, SendOtpNotification::class);
    }

    /**
     * Requirement: [ ] Password Recovery
     */
    public function test_password_recovery_screen_can_be_rendered()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
    }

    /**
     * Requirement: [ ] Password Policy
     * Min 8 chars, uppercase, lowercase, number, special char
     */
    public function test_password_must_meet_complexity_policy()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'simple',
            'password_confirmation' => 'simple',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Requirement: [ ] Session Management - Concurrent session handling
     */
    public function test_user_can_logout_other_browser_sessions()
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('profile.logout-other-sessions'), [
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'sessions-cleared');
    }
}
