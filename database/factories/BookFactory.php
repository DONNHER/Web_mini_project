<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected static $categoryIds = null;

    protected static $publishers = [
        'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Macmillan Publishers', 'Hachette Book Group', 'Scholastic',
        'Bloomsbury Publishing', 'Oxford University Press', 'Cambridge University Press',
        'Pearson Education', 'Elsevier', 'Wiley', 'Kogan Page',
        'Sourcebooks', 'Chronicle Books'
    ];

    public function definition(): array
    {
        // Category ID Caching
        if (self::$categoryIds === null) {
            self::$categoryIds = Category::pluck('id')->toArray();
            if (empty(self::$categoryIds)) {
                self::$categoryIds = [Category::factory()->create()->id];
            }
        }

        // Format-based pricing
        $format = $this->faker->randomElement(['Paperback', 'Hardcover', 'E-book', 'Audiobook']);
        $basePrice = match($format) {
            'Paperback' => $this->faker->randomFloat(2, 9.99, 19.99),
            'Hardcover' => $this->faker->randomFloat(2, 24.99, 49.99),
            'E-book'    => $this->faker->randomFloat(2, 4.99, 14.99),
            'Audiobook' => $this->faker->randomFloat(2, 19.99, 39.99),
        };

        return [
            'isbn' => $this->generateValidIsbn13(),
            'title' => $this->faker->sentence(rand(2, 6)),
            'author' => $this->faker->name(),
            'publisher' => $this->faker->randomElement(self::$publishers),
            'price' => $basePrice,
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'category_id' => $this->faker->randomElement(self::$categoryIds),
            'format' => $format,
            'is_active' => $this->faker->boolean(85), // 85% active
            'published_at' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'publication_year' => 2024,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Bestseller State
     */
    public function bestseller(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(500, 5000),
            'is_active' => true,
        ]);
    }

    /**
     * Valid ISBN-13 generation with modulo-10 checksum
     */
    protected function generateValidIsbn13(): string
    {
        $prefix = '978';
        $body = str_pad((string) mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        $isbn = $prefix . $body;

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $isbn[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checksum = (10 - ($sum % 10)) % 10;
        return $isbn . $checksum;
    }

    /**
     * Optimized Bulk Data Generation
     */
    public function bulkData($count, $categoryIds, $authorIds = [], $publisherIds = [])
    {
        $data = [];
        $now = now();

        for ($i = 0; $i < $count; $i++) {
            $format = ['Paperback', 'Hardcover', 'E-book', 'Audiobook'][array_rand(['Paperback', 'Hardcover', 'E-book', 'Audiobook'])];
            $price = match($format) {
                'Paperback' => mt_rand(999, 1999) / 100,
                'Hardcover' => mt_rand(2499, 4999) / 100,
                'E-book'    => mt_rand(499, 1499) / 100,
                'Audiobook' => mt_rand(1999, 3999) / 100,
            };

            $data[] = [
                'title' => 'Bulk Book Title ' . mt_rand(1, 1000000),
                'author' => 'Bulk Author ' . mt_rand(1, 50000),
                'publisher' => self::$publishers[array_rand(self::$publishers)],
                'isbn' => 'TEMP_' . mt_rand() . '_' . $i,
                'price' => $price,
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'author_id' => !empty($authorIds) ? $authorIds[array_rand($authorIds)] : null,
                'publisher_id' => !empty($publisherIds) ? $publisherIds[array_rand($publisherIds)] : null,
                'format' => $format,
                'published_at' => $now->copy()->subDays(mt_rand(0, 3650)),
                'is_active' => true,
                'stock_quantity' => mt_rand(0, 1000),
                'created_at' => $now,
                'updated_at' => $now,
                'publication_year' => $now->year,
            ];
        }
        return $data;
    }
}
