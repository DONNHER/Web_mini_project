<?php

namespace Database\Seeders;

use App\Models\LoanProduct;
use App\Models\LoanCategory;
use Illuminate\Database\Seeder;

class LoanProductSeeder extends Seeder
{
    public function run(): void
    {
        $personal = LoanCategory::where('name', 'Personal Loans')->first();
        $business = LoanCategory::where('name', 'Business Loans')->first();
        $emergency = LoanCategory::where('name', 'Emergency Loans')->first();

        LoanProduct::create([
            'category_id' => $personal->id,
            'name' => 'Salary Loan',
            'description' => 'Fast cash loan based on your monthly salary.',
            'interest_rate' => 3.5,
            'duration_months' => 6,
            'min_amount' => 5000,
            'max_amount' => 50000,
            'is_active' => true,
        ]);

        LoanProduct::create([
            'category_id' => $personal->id,
            'name' => 'Home Renovation Loan',
            'description' => 'Upgrade your home with this specialized loan.',
            'interest_rate' => 5.0,
            'duration_months' => 24,
            'min_amount' => 50000,
            'max_amount' => 500000,
            'is_active' => true,
        ]);

        LoanProduct::create([
            'category_id' => $business->id,
            'name' => 'SME Starter Loan',
            'description' => 'Perfect for small businesses looking to expand their inventory.',
            'interest_rate' => 4.5,
            'duration_months' => 12,
            'min_amount' => 20000,
            'max_amount' => 200000,
            'is_active' => true,
        ]);

        LoanProduct::create([
            'category_id' => $emergency->id,
            'name' => 'Quick Cash Emergency',
            'description' => 'Released within 24 hours for verified borrowers.',
            'interest_rate' => 7.0,
            'duration_months' => 3,
            'min_amount' => 1000,
            'max_amount' => 10000,
            'is_active' => true,
        ]);
    }
}
