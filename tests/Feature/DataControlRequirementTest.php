<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\LoanProduct;
use App\Models\LoanCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataControlRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Multi-parameter filtering, [ ] Field-specific sorting
     */
    public function test_admin_can_filter_and_sort_users()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        User::factory()->create(['name' => 'Alpha', 'status' => 'active']);
        User::factory()->create(['name' => 'Zeta', 'status' => 'suspended']);

        // 1. Filter by status
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['status' => 'suspended']));
        $response->assertSee('Zeta');
        $response->assertDontSee('Alpha');

        // 2. Sort by name DESC
        $responseSort = $this->actingAs($admin)->get(route('admin.users.index', ['sort' => 'name', 'direction' => 'desc']));
        $responseSort->assertSeeInOrder(['Zeta', 'Alpha']);
    }

    /**
     * Requirement: [ ] Bulk actions (Bulk Delete, Bulk Activate)
     */
    public function test_admin_can_perform_bulk_actions_on_users()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $user1 = User::factory()->create(['status' => 'inactive']);
        $user2 = User::factory()->create(['status' => 'suspended']);

        $this->actingAs($admin)->get(route('admin.users.index', [
            'bulk_action' => 'activate',
            'selected_users' => [$user1->id, $user2->id]
        ]));

        $this->assertEquals('active', $user1->refresh()->status);
        $this->assertEquals('active', $user2->refresh()->status);
    }

    /**
     * Requirement: [ ] Advanced pagination
     */
    public function test_admin_can_control_pagination_size()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        User::factory()->count(15)->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['per_page' => 5]));

        // Should see 5 users per page
        $this->assertCount(5, $response->viewData('users'));
    }
}
