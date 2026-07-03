<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiction', 'description' => 'Literary fiction books'],
            ['name' => 'Mystery', 'description' => 'Mystery and thriller books'],
            ['name' => 'Science', 'description' => 'Science and technology'],
            ['name' => 'Biography', 'description' => 'Biographical accounts'],
            ['name' => 'History', 'description' => 'Historical non-fiction'],
            ['name' => 'Fantasy', 'description' => 'Fantasy and magic'],
            ['name' => 'Horror', 'description' => 'Scary and suspenseful'],
            ['name' => 'Romance', 'description' => 'Love and relationships'],
            ['name' => 'Self-Help', 'description' => 'Personal development'],
            ['name' => 'Travel', 'description' => 'Travel guides and stories'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
