<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_caching_works()
    {
        // 1. Create a category
        $category = Category::factory()->create(['name' => 'Fiction']);

        // 2. Cache categories
        $cached = Category::getCached();
        $this->assertTrue($cached->contains('name', 'Fiction'));
        $this->assertTrue(Cache::has('all_categories'));

        // 3. Update category name directly in DB to bypass Eloquent events
        DB::table('categories')->where('id', $category->id)->update(['name' => 'Non-Fiction']);

        // 4. Retrieve cached categories - should still be 'Fiction'
        $cachedAgain = Category::getCached();
        $this->assertEquals('Fiction', $cachedAgain->firstWhere('id', $category->id)->name);

        // 5. Update via Eloquent - should clear cache
        $category->name = 'Science';
        $category->save();

        $this->assertFalse(Cache::has('all_categories'));

        // 6. Retrieve again - should be 'Science'
        $final = Category::getCached();
        $this->assertEquals('Science', $final->firstWhere('id', $category->id)->name);
    }

    public function test_bestseller_caching_works()
    {
        // 1. Setup data
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantity' => 5
        ]);

        // 2. Get bestsellers
        $bestsellers = Book::getBestsellers(5);
        $this->assertCount(1, $bestsellers);
        $this->assertTrue(Cache::has('bestseller_books_5'));

        // 3. Modify book title directly in DB
        DB::table('books')->where('id', $book->id)->update(['title' => 'Cached Title']);

        // 4. Get bestsellers again - should be old title
        $bestsellersAgain = Book::getBestsellers(5);
        $this->assertNotEquals('Cached Title', $bestsellersAgain->first()->title);

        // 5. Save via Eloquent - should clear cache
        $book->touch();
        $this->assertFalse(Cache::has('bestseller_books_5'));
    }

    public function test_eager_loading_in_home_page()
    {
        Category::factory()->count(3)->hasBooks(2)->create();

        DB::enableQueryLog();

        $this->get(route('home'));

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Check if categories are loaded with book counts
        // Usually one query for books with category, one for categories with count
        $this->assertLessThan(10, count($queries), 'Too many queries detected. Eager loading might be missing.');
    }

    public function test_eager_loading_in_admin_dashboard()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Setup some data
        $customer = User::factory()->create();
        $book = Book::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'book_id' => $book->id]);

        DB::enableQueryLog();

        $this->get(route('admin.dashboard'));

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // We expect eager loading for 'user' on orders, and 'user'/'book' on reviews
        // If lazy loading occurred, query count would be higher.
        $this->assertLessThan(15, count($queries), 'High query count detected in Admin Dashboard.');
    }
}
