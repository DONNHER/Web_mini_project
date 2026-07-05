<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\LoanProduct;
use App\Models\LoanCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormValidationRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Server-side validation with proper error messages
     */
    public function test_loan_product_creation_requires_valid_data()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.loan-products.store'), [
            'name' => '', // Required
            'interest_rate' => 'not-a-number', // Numeric
            'max_amount' => 100, // Should be >= min_amount
            'min_amount' => 500,
        ]);

        $response->assertSessionHasErrors(['name', 'interest_rate', 'max_amount']);
    }

    /**
     * Requirement: [ ] Auto-save draft functionality (Requirement 12.3)
     * Verification: Verified via presence of JS logic in the 'loans.apply' view.
     */
    public function test_loan_application_view_contains_autosave_logic()
    {
        $user = User::factory()->create(['status' => 'active', 'role_id' => Role::where('name', 'borrower')->first()->id]);
        $product = LoanProduct::factory()->create();

        $response = $this->actingAs($user)->get(route('loans.apply', $product));

        $response->assertSee('localStorage.setItem(storageKey');
        $response->assertSee('loan_draft_');
    }
}
