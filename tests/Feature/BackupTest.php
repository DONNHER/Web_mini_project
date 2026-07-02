<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function it_allows_admin_to_trigger_manual_backup()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Artisan::spy();

        $response = $this->post(route('admin.dashboard.backup'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Artisan::shouldHaveReceived('queue')->with('backup:run');
    }

    /** @test */
    public function it_denies_non_admin_to_trigger_manual_backup()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        $response = $this->post(route('admin.dashboard.backup'));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_verifies_backup_scheduling_is_configured()
    {
        $schedule = app()->make(Schedule::class);

        $events = collect($schedule->events());

        $hasDbBackup = $events->contains(function ($event) {
            return str_contains($event->command, 'backup:run') && str_contains($event->command, '--only-db');
        });

        $hasClean = $events->contains(function ($event) {
            return str_contains($event->command, 'backup:clean');
        });

        $hasMonitor = $events->contains(function ($event) {
            return str_contains($event->command, 'backup:monitor');
        });

        $this->assertTrue($hasDbBackup, 'Daily database backup is not scheduled.');
        $this->assertTrue($hasClean, 'Backup cleanup is not scheduled.');
        $this->assertTrue($hasMonitor, 'Backup monitoring is not scheduled.');
    }

    /** @test */
    public function it_sends_notifications_on_backup_failure()
    {
        Notification::fake();

        // Simulate the event that spatie/laravel-backup fires
        event(new BackupHasFailed(new \Exception('Storage full'), null));

        Notification::assertSentTo(
            new \Spatie\Backup\Notifications\Notifiable(),
            BackupHasFailedNotification::class
        );
    }

    /** @test */
    public function it_verifies_backup_retention_policy_configuration()
    {
        $config = config('backup.cleanup.default_strategy');

        $this->assertEquals(7, $config['keep_all_backups_for_days']);
        $this->assertEquals(4, $config['keep_weekly_backups_for_weeks']);
    }

    /** @test */
    public function it_verifies_backup_destination_is_configured()
    {
        $disks = config('backup.backup.destination.disks');

        $this->assertContains('local', $disks);
        $this->assertNotEmpty(config('backup.backup.name'));
        // Check if it matches the expected name from config or environment
        $this->assertTrue(
            in_array(config('backup.backup.name'), ['PageTurner-System', 'Laravel', env('APP_NAME')]),
            'Backup name does not match expected project naming.'
        );
    }
}
