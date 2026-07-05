<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Loan;
use App\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Administrator has access, Responsive grid layout
     */
    public function test_admin_can_access_dashboard_with_all_widgets()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Verify presence of required widgets in HTML
        $response->assertSee('Total Users');
        $response->assertSee('Total Disbursed');
        $response->assertSee('Database Size');
        $response->assertSee('User Registrations');
        $response->assertSee('Loan Activity');
        $response->assertSee('Quick Actions');
        $response->assertSee('Recent Activities');
    }

    /**
     * Requirement: [ ] Refresh data without page reload (AJAX)
     */
    public function test_stats_api_returns_json_for_ajax_refresh()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.dashboard.stats'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'totalUsers',
            'activeNow',
            'registrations',
            'totalLoanProducts',
            'totalLoans',
            'totalDisbursed',
            'loanStatusSummary',
            'monthlyActivity',
            'dbSize',
            'errorRate',
            'recentActivities',
        ]);
    }

    /**
     * Requirement: [ ] Date range filters (Today, Week, Month, Custom)
     */
    public function test_stats_api_respects_date_range_filter()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
            'created_at' => now(),
        ]);

        // Create a user from 10 days ago
        User::factory()->create(['created_at' => now()->subDays(10)]);

        // 1. Week range (default) - should include only the admin (created now)
        $responseWeek = $this->actingAs($admin)->getJson(route('admin.dashboard.stats', ['range' => 'week']));
        $this->assertEquals(1, collect($responseWeek->json('registrations'))->sum('total'));

        // 2. Month range - SHOULD include both the admin and the 10-day-old user
        $responseMonth = $this->actingAs($admin)->getJson(route('admin.dashboard.stats', ['range' => 'month']));
        $this->assertEquals(2, collect($responseMonth->json('registrations'))->sum('total'));
    }

    /**
     * Requirement: [ ] Quick Actions Shortcut buttons
     */
    public function test_dashboard_contains_functional_quick_action_links()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertSee(route('admin.users.index'));
        $response->assertSee(route('admin.loans.index'));
        $response->assertSee(route('admin.audit-logs.index'));
        $response->assertSee(route('admin.ai-security.index'));
    }

    /**
     * Requirement: [ ] Recent Activities Real-time feed
     */
    public function test_dashboard_shows_latest_audit_activities()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        // Create a dummy audit entry
        Audit::create([
            'user_id' => $admin->id,
            'event' => 'custom_action',
            'auditable_type' => 'System',
            'auditable_id' => 0,
            'new_values' => ['foo' => 'bar'],
            'url' => 'http://localhost',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertSee('custom action');
        $response->assertSee($admin->name);
    }

    public function test_non_admin_cannot_access_dashboard()
    {
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'borrower')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
