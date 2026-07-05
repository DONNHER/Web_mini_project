<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarningRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Destructive Action Alerts
     * Verification: Confirmation dialogs are present in frontend. Backend requires password for deletion.
     */
    public function test_destructive_action_requires_password_confirmation()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
            'password' => bcrypt('Password123!'),
        ]);

        $userToDelete = User::factory()->create();

        // Attempt delete without password
        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $userToDelete));
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);

        // Attempt delete with correct password
        $this->actingAs($admin)->delete(route('admin.users.destroy', $userToDelete), [
            'password' => 'Password123!',
        ]);

        $this->assertSoftDeleted('users', ['id' => $userToDelete->id]);
    }

    /**
     * Requirement: [ ] Session Expiry Warning
     * Verification: The layout contains the script for session expiration.
     */
    public function test_session_expiry_warning_script_is_present()
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertSee('Session Expiring');
        $response->assertSee('setTimeout(function()');
    }

    /**
     * Requirement: [ ] Unsaved Changes Warning
     * Verification: Forms have the 'dirty-check' class.
     */
    public function test_unsaved_changes_warning_class_is_present_on_forms()
    {
        $user = User::factory()->create(['status' => 'active', 'role_id' => Role::where('name', 'borrower')->first()->id]);

        // Check profile edit form
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertSee('dirty-check');

        // Check loan application form
        $product = \App\Models\LoanProduct::factory()->create();
        $response = $this->actingAs($product->category_id ? $user : $user)->get(route('loans.apply', $product));
        $response->assertSee('dirty-check');
    }

    /**
     * Requirement: [ ] Security Warnings - suspicious attempts
     */
    public function test_failed_login_attempts_log_security_audit()
    {
        $user = User::factory()->create(['email' => 'victim@example.com']);

        // Failed attempt
        $this->post(route('login'), [
            'email' => 'victim@example.com',
            'password' => 'wrong',
        ]);

        $this->assertDatabaseHas('audits', [
            'event' => 'login_failed',
            'new_values' => json_encode(['email' => 'victim@example.com']),
        ]);
    }
}
