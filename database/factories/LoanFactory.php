<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\LoanProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'loan_product_id' => LoanProduct::factory(),
            'principal_amount' => $this->faker->randomFloat(2, 5000, 20000),
            'interest_rate' => 5,
            'term_months' => 12,
            'total_amount' => $this->faker->randomFloat(2, 5500, 22000),
            'status' => 'pending',
            'payment_method' => 'Bank Transfer',
            'due_date' => now()->addMonths(12),
        ];
    }
}
