<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\LoanProduct;
use App\Models\LoanCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudStandardsRequirementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Requirement: [ ] Soft delete with "Restore" capability
     */
    public function test_loan_products_can_be_soft_deleted_and_restored()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $product = LoanProduct::factory()->create();

        // 1. Delete
        $this->actingAs($admin)->delete(route('admin.loan-products.destroy', $product), [
            'password' => 'password',
        ]);

        $this->assertSoftDeleted('loan_products', ['id' => $product->id]);

        // 2. Restore
        $this->actingAs($admin)->post(route('admin.loan-products.restore', $product->id));

        $this->assertDatabaseHas('loan_products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Requirement: [ ] Optimistic locking for data integrity
     */
    public function test_optimistic_locking_prevents_concurrent_edits()
    {
        $product = LoanProduct::factory()->create(['version' => 1]);

        // User A loads the product
        $productA = LoanProduct::find($product->id);

        // User B loads and updates the product
        $productB = LoanProduct::find($product->id);
        $productB->name = 'Updated by B';
        $productB->save();
        $this->assertEquals(2, $productB->version);

        // User A tries to update with old version (Eloquent boot logic usually handles this via my trait)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Record was modified by another user');

        $productA->name = 'Updated by A';
        $productA->save();
    }
}
