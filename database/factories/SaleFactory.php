<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wilaya>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 48),
            'total_price' => $this->faker->randomFloat(2, 5, 100),
        ];
    }
}
