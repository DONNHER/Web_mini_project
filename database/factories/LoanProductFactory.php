<?php

namespace Database\Factories;

use App\Models\LoanCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => LoanCategory::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'interest_rate' => $this->faker->randomFloat(2, 1, 15),
            'duration_months' => $this->faker->randomElement([3, 6, 12, 24, 36]),
            'min_amount' => $this->faker->randomFloat(2, 1000, 5000),
            'max_amount' => $this->faker->randomFloat(2, 10000, 100000),
            'is_active' => true,
        ];
    }
}
