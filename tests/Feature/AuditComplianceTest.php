<?php

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Category::factory()->create(['name' => 'Fiction']);
    }

    /** @test */
    public function it_creates_audit_entries_for_book_crud_operations()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. Create (Audit entry for 'created')
        $book = Book::create([
            'category_id' => Category::first()->id,
            'title' => 'Audit Test Book',
            'author' => 'Test Author',
            'isbn' => '1234567890',
            'price' => 19.99,
            'stock_quantity' => 10,
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Book::class,
            'auditable_id' => $book->id,
            'event' => 'created',
            'user_id' => $admin->id,
        ]);

        // 2. Update (Audit entry for 'updated')
        $book->update(['price' => 24.99]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Book::class,
            'auditable_id' => $book->id,
            'event' => 'updated',
            'user_id' => $admin->id,
        ]);

        // 3. Delete (Audit entry for 'deleted')
        $book->delete();

        $this->assertDatabaseHas('audits', [
            'auditable_type' => Book::class,
            'auditable_id' => $book->id,
            'event' => 'deleted',
            'user_id' => $admin->id,
        ]);
    }

    /** @test */
    public function it_excludes_sensitive_data_like_passwords_from_audit_logs()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($user);

        // Update password
        $user->update([
            'password' => Hash::make('new-password'),
            'name' => 'Updated Name'
        ]);

        $audit = Audit::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($audit);

        // Verify 'password' is not in old or new values
        $this->assertArrayNotHasKey('password', $audit->old_values);
        $this->assertArrayNotHasKey('password', $audit->new_values);

        // But 'name' should be there
        $this->assertArrayHasKey('name', $audit->new_values);
    }

    /** @test */
    public function it_verifies_tamper_proof_checksums_on_audit_logs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $book = Book::create([
            'category_id' => Category::first()->id,
            'title' => 'Checksum Book',
            'author' => 'Author',
            'isbn' => '0987654321',
            'price' => 10.00,
            'stock_quantity' => 5,
        ]);

        $audit = Audit::where('auditable_type', Book::class)->first();

        // Verify checksum exists and is valid
        $this->assertNotNull($audit->checksum);
        $this->assertTrue($audit->isValid());

        // Verify that updating an audit log throws an exception (tamper-proof)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Audit logs are tamper-proof and cannot be updated.');

        $audit->update(['event' => 'malicious_change']);
    }

    /** @test */
    public function it_allows_searching_and_filtering_audit_logs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create some audits
        Book::create(['category_id' => Category::first()->id, 'title' => 'Book A', 'author' => 'A', 'isbn' => '1', 'price' => 1, 'stock_quantity' => 1]);
        User::factory()->create(['name' => 'User B']);

        // Filter by Book model
        $response = $this->get(route('admin.audit-logs.index', ['auditable_type' => 'Book']));
        $response->assertStatus(200);
        $response->assertSee('Book');

        $audits = $response->viewData('audits');
        foreach ($audits as $audit) {
            $this->assertEquals(Book::class, $audit->auditable_type);
        }

        // Filter by User model
        $response = $this->get(route('admin.audit-logs.index', ['auditable_type' => 'User']));
        $response->assertStatus(200);
        $audits = $response->viewData('audits');
        foreach ($audits as $audit) {
            $this->assertEquals(User::class, $audit->auditable_type);
        }

        // Filter by event
        $response = $this->get(route('admin.audit-logs.index', ['event' => 'created']));
        $response->assertStatus(200);
        $audits = $response->viewData('audits');
        foreach ($audits as $audit) {
            $this->assertEquals('created', $audit->event);
        }
    }
}
