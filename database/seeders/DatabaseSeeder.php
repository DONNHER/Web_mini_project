<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create admin user
        if (!User::where('email', 's.cuarteros.dionherlove@cmu.edu.ph')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 's.cuarteros.dionherlove@cmu.edu.ph',
                'role' => 'admin',
            ]);
        }

        // 2. Create specific categories for Import testing
        $categoryNames = ['Fiction', 'Mystery', 'Science', 'Biography', 'History', 'Fantasy'];
        foreach ($categoryNames as $name) {
            Category::firstOrCreate(['name' => $name], ['description' => "Books related to $name"]);
        }

        // 3. Create customer users
        if (User::where('role', 'customer')->count() < 10) {
            User::factory(10)->create(['role' => 'customer']);
        }

        $customers = User::where('role', 'customer')->get();
        $categories = Category::all();

        // 4. Create some initial books
        if (Book::count() < 5) {
            foreach ($categories->take(3) as $category) {
                Book::factory(2)->create(['category_id' => $category->id]);
            }
        }

        $books = Book::all();

        // 5. Create reviews (Ensuring unique user_id per book_id)
        if (Review::count() == 0 && $customers->count() > 0 && $books->count() > 0) {
            foreach ($books as $book) {
                $count = min(fake()->numberBetween(1, 3), $customers->count());
                $reviewers = $customers->random($count);

                foreach ($reviewers as $reviewer) {
                    Review::factory()->create([
                        'user_id' => $reviewer->id,
                        'book_id' => $book->id,
                    ]);
                }
            }
        }

        // 6. Run Monitoring Seeder (Requirement 10.2)
        $this->call([
            MonitoringSeeder::class,
        ]);
    }
}
