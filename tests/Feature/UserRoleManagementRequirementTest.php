<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserRoleManagementRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and permissions as defined in RolesAndPermissionsSeeder
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Role-based access control (RBAC)
     * Administrator: Full system control
     */
    public function test_administrator_has_full_system_control()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasPermission('users.manage'));
        $this->assertTrue($admin->hasPermission('settings.edit'));

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Requirement: [ ] Role-based access control (RBAC)
     * Standard User: Regular operations, Assigned modules only
     */
    public function test_standard_user_has_limited_access()
    {
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'borrower')->first()->id,
            'status' => 'active',
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->hasPermission('loans.apply'));
        $this->assertFalse($user->hasPermission('users.manage'));

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * Requirement: [ ] Permission assignment per module
     */
    public function test_permission_assignment_per_module()
    {
        $borrowerRole = Role::where('name', 'borrower')->first();

        // Check if permissions are correctly grouped by module in the seeder
        $permissions = Permission::where('module', 'Loan Products')->get();
        $this->assertNotEmpty($permissions);

        $user = User::factory()->create(['role_id' => $borrowerRole->id]);

        // Borrower should have view access but maybe not delete access for loan products
        $this->assertTrue($user->hasPermission('loan_products.view'));
        $this->assertFalse($user->hasPermission('loan_products.delete'));
    }

    /**
     * Requirement: [ ] User status management (Active/Inactive/Suspended)
     */
    public function test_user_status_management_active()
    {
        $user = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));
        $response->assertStatus(200);
    }

    public function test_user_status_management_inactive()
    {
        $user = User::factory()->create(['status' => 'inactive', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_status_management_suspended()
    {
        $user = User::factory()->create(['status' => 'suspended', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Requirement: [ ] Profile management with avatar upload
     */
    public function test_profile_management_with_avatar_upload()
    {
        Storage::fake('public');

        $user = User::factory()->create(['status' => 'active', 'email_verified_at' => now()]);
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'John Doe',
            'email' => 'new-email@example.com',
            'avatar' => $avatar,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'email' => 'new-email@example.com',
        ]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Requirement: [ ] Password reset functionality
     */
    public function test_password_reset_functionality_request_page()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
    }

    public function test_password_reset_link_can_be_sent()
    {
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        // Depending on mail config in tests, it might be successful or redirected
        $response->assertSessionHasNoErrors();
    }
}
