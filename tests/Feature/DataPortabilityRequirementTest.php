<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\LoanProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class DataPortabilityRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Data Export - JSON format
     */
    public function test_user_can_export_personal_data_to_json()
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->get(route('user.export.personal'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonFragment(['email' => $user->email]);
    }

    /**
     * Requirement: [ ] Data Export - XLSX format (Admin)
     */
    public function test_admin_can_export_loan_products_to_excel()
    {
        Excel::fake();

        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.loan-products.export', ['format' => 'xlsx']));

        $response->assertStatus(200);
        Excel::assertDownloaded('loan_products_export_' . now()->format('Y-m-d_His') . '.xlsx');
    }

    /**
     * Requirement: [ ] Data Redaction Option
     */
    public function test_admin_can_export_users_with_pii_redaction()
    {
        Excel::fake();

        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.export', ['redact_pii' => '1']));

        $response->assertStatus(200);
        Excel::assertDownloaded('users_export_' . now()->format('Y-m-d_His') . '.xlsx');
    }

    /**
     * Requirement: [ ] Bulk Import, [ ] Import Logs
     */
    public function test_admin_can_queue_loan_product_import()
    {
        Excel::fake();
        Storage::fake('local');

        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('products.csv');

        $response = $this->actingAs($admin)->post(route('admin.loan-products.import'), [
            'file' => $file,
            'duplicate_action' => 'skip',
        ]);

        $response->assertSessionHas('success');

        $log = \App\Models\ImportExportLog::where('type', 'import')->first();
        $this->assertNotNull($log);
        $this->assertEquals($admin->id, $log->user_id);

        Excel::assertImported('imports/' . $log->file_name, 'local');
    }
}
