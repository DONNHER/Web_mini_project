<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_amount' => fake()->randomFloat(2, 20, 500),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'shipping_address' => fake()->address(),
        ];
    }
}
