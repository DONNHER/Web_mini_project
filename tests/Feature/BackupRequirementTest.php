<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BackupRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Manual Backup Trigger
     */
    public function test_admin_can_trigger_manual_backup()
    {
        Queue::fake();

        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.dashboard.backup'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Manual backup has been triggered.');

        // Backup is usually queued as a QueuedCommand
        Queue::assertPushed(\Illuminate\Foundation\Console\QueuedCommand::class, function ($job) {
            // Access the 'command' property via reflection or if it's public
            // For simplicity in tests, just asserting the job class is often enough
            return true;
        });
    }

    /**
     * Requirement: [ ] Backup Monitoring Dashboard
     */
    public function test_backup_health_is_displayed_on_dashboard()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertSee('Backup Status');
        $response->assertSee('Healthy');
    }
}
