<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = new Faker();
        return [
            'shop_id' => Shop::inRandomOrder()->first()->id,
            'details' => $this->faker->paragraph,
            'name' => $this->faker->words(3, true),
            'sizes' => array_rand(['Small', 'Medium', 'Large']),
            'stock' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'item_type_id' => $this->faker->numberBetween(1, 5),
            'gender_id' => $this->faker->randomElement([1, 2, 3]),
            'images' => json_encode([$this->faker->imageUrl(480, 480), $this->faker->imageUrl(480, 480), $this->faker->imageUrl(480, 480)])
        ];
    }
}
