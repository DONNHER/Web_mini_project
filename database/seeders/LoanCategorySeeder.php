<?php

namespace Database\Seeders;

use App\Models\LoanCategory;
use Illuminate\Database\Seeder;

class LoanCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Personal Loans', 'description' => 'Loans for personal expenses like travel, medical, or education.'],
            ['name' => 'Business Loans', 'description' => 'Loans for starting or growing a business.'],
            ['name' => 'Auto Loans', 'description' => 'Loans for purchasing vehicles.'],
            ['name' => 'Emergency Loans', 'description' => 'Quick loans for urgent financial needs.'],
        ];

        foreach ($categories as $category) {
            LoanCategory::create($category);
        }
    }
}
