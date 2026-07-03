<?php

namespace Database\Seeders;

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

        // 2. Step 3 - Mass Seeding Orchestration
        $this->call([
            CategorySeeder::class, // Must run before books
            MassBookSeeder::class,
        ]);

        // 3. Warm up cache for categories
        \App\Models\Category::all()->each(function ($category) {
            \App\Jobs\WarmCategoryCache::dispatch($category->id);
        });
    }
}
