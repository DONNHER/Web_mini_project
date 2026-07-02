<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class DataPortabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Excel::fake();
    }

    /** @test */
    public function user_can_export_personal_data_as_json()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        $this->actingAs($user);

        // Create related data
        $book = Book::factory()->create(['title' => 'Test Book']);
        $order = Order::factory()->create(['user_id' => $user->id, 'total_amount' => 150.00]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantity' => 2,
            'unit_price' => 75.00
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'Excellent read'
        ]);

        $response = $this->get(route('user.export.personal'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonPath('personal_info.name', 'John Doe');
        $response->assertJsonCount(1, 'orders');
        $response->assertJsonCount(1, 'reviews');
    }

    /** @test */
    public function user_can_export_order_history_as_excel()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('user.export.orders.excel'));

        $response->assertStatus(200);
        Excel::assertDownloaded('order_history.xlsx');
    }

    /** @test */
    public function user_can_export_order_history_as_pdf()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create an order with items so the template has data to loop through
        $book = Book::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantity' => 1
        ]);

        $response = $this->get(route('user.export.orders.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function user_can_export_reading_history_as_json()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Purchase history
        $book1 = Book::factory()->create(['title' => 'Purchased Book']);
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
        OrderItem::factory()->create(['order_id' => $order->id, 'book_id' => $book1->id]);

        // Review history
        $book2 = Book::factory()->create(['title' => 'Reviewed Book']);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id]);

        $response = $this->get(route('user.export.reading'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'reading_and_purchase_history' => [
                '*' => ['type', 'title', 'author', 'date']
            ]
        ]);

        // Ensure both purchase and review types are present
        $types = collect($response->json('reading_and_purchase_history'))->pluck('type');
        $this->assertTrue($types->contains('Purchase'));
        $this->assertTrue($types->contains('Review'));
    }

    /** @test */
    public function guest_cannot_access_data_portability_exports()
    {
        $this->get(route('user.export.personal'))->assertRedirect(route('login'));
        $this->get(route('user.export.orders.excel'))->assertRedirect(route('login'));
        $this->get(route('user.export.orders.pdf'))->assertRedirect(route('login'));
        $this->get(route('user.export.reading'))->assertRedirect(route('login'));
    }
}
