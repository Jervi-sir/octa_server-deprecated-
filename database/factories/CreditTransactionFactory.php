<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentHistory>
 */
class CreditTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'amount' => $this->faker->randomNumber(3),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}
