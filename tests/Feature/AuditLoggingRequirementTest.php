<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Audit;
use App\Models\LoanProduct;
use App\Models\LoanCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Tests\TestCase;

class AuditLoggingRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Authentication Logs
     */
    public function test_authentication_attempts_are_logged()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!'),
            'status' => 'active',
        ]);

        // 1. Log in
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'event' => 'login',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);

        // 2. Log out
        $this->actingAs($user)->post(route('logout'));

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'event' => 'logout',
        ]);

        // 3. Failed Login
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('audits', [
            'event' => 'login_failed',
        ]);
    }

    /**
     * Requirement: [ ] Transaction Logs (CRUD)
     */
    public function test_crud_operations_are_logged()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $category = LoanCategory::create(['name' => 'Test Category']);

        // 1. Create (Store)
        $response = $this->actingAs($admin)->post(route('admin.loan-products.store'), [
            'category_id' => $category->id,
            'name' => 'New Product',
            'description' => 'Description',
            'interest_rate' => 5.0,
            'duration_months' => 12,
            'min_amount' => 1000,
            'max_amount' => 5000,
        ]);

        $product = LoanProduct::where('name', 'New Product')->first();

        $this->assertDatabaseHas('audits', [
            'user_id' => $admin->id,
            'event' => 'created',
            'auditable_type' => LoanProduct::class,
            'auditable_id' => $product->id,
        ]);

        // 2. Update
        $this->actingAs($admin)->patch(route('admin.loan-products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Updated Product',
            'interest_rate' => 6.0,
            'duration_months' => 12,
            'min_amount' => 1000,
            'max_amount' => 5000,
        ]);

        $audit = Audit::where('event', 'updated')->where('auditable_id', $product->id)->first();
        $this->assertNotNull($audit);
        $this->assertEquals('New Product', $audit->old_values['name']);
        $this->assertEquals('Updated Product', $audit->new_values['name']);

        // 3. Delete
        $this->actingAs($admin)->delete(route('admin.loan-products.destroy', $product), [
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('audits', [
            'event' => 'deleted',
            'auditable_id' => $product->id,
        ]);
    }

    /**
     * Requirement: [ ] Error Logs
     */
    public function test_system_errors_are_logged_to_audits()
    {
        Log::error('Test error message', ['source' => 'PHPUnit']);

        $this->assertDatabaseHas('audits', [
            'event' => 'error_logged',
            'auditable_type' => 'System',
        ]);
    }

    /**
     * Requirement: [ ] Access Logs
     */
    public function test_page_visits_are_logged()
    {
        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($user)->get(route('loan_products.index'));

        $this->assertDatabaseHas('audits', [
            'user_id' => $user->id,
            'event' => 'accessed',
            'auditable_type' => 'Page',
        ]);
    }

    /**
     * Requirement: [ ] Searchable and filterable log viewer (Admin only)
     */
    public function test_audit_log_viewer_is_accessible_to_admin_only()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role_id' => Role::where('name', 'borrower')->first()->id,
            'status' => 'active',
        ]);

        $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertStatus(403);
    }

    /**
     * Requirement: [ ] Auto-archive logs older than 90 days
     */
    public function test_logs_older_than_90_days_are_archived()
    {
        Storage::fake('local');

        $oldAudit = new Audit();
        $oldAudit->event = 'old_event';
        $oldAudit->auditable_type = 'System';
        $oldAudit->auditable_id = 0;
        $oldAudit->created_at = Carbon::now()->subDays(100);
        $oldAudit->save();

        $newAudit = new Audit();
        $newAudit->event = 'new_event';
        $newAudit->auditable_type = 'System';
        $newAudit->auditable_id = 0;
        $newAudit->created_at = Carbon::now();
        $newAudit->save();

        $this->artisan('audit:archive')->assertExitCode(0);

        $this->assertDatabaseMissing('audits', ['event' => 'old_event']);
        $this->assertDatabaseHas('audits', ['event' => 'new_event']);

        $files = Storage::disk('local')->files('archives');
        $this->assertNotEmpty($files);
    }

    /**
     * Requirement: [ ] Visual indicators for suspicious activities
     * Verified via isCriticalEvent logic
     */
    public function test_suspicious_activity_is_detected()
    {
        $audit = new Audit();
        $audit->event = 'login_failed';
        $audit->auditable_type = 'User';
        $audit->auditable_id = 0;

        $this->assertTrue($audit->isCriticalEvent());

        $rateJumpAudit = new Audit();
        $rateJumpAudit->event = 'updated';
        $rateJumpAudit->auditable_type = LoanProduct::class;
        $rateJumpAudit->old_values = ['interest_rate' => 5];
        $rateJumpAudit->new_values = ['interest_rate' => 15];

        $this->assertTrue($rateJumpAudit->isCriticalEvent());
    }
}
