<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Shop;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $types = ['', 'shirt', 'pant', 'shoe', 'watch', 'other'];
        $gender = ['', 'male', 'female', 'mixte'];
        $details = $this->faker->paragraph;
        $name = $this->faker->words(3, true);
        $sizes = array_rand(['Small', 'Medium', 'Large']);
        $item_type_id = $this->faker->numberBetween(1, 4);
        $item_type_name = $types[$item_type_id];
        //$genders = $this->faker->randomElement([1, 2, 3]);
        //$gender_name = $gender[$gender_id];
        $shop = Shop::inRandomOrder()->first();
        return [
            'shop_id' => $shop->id,
            'details' => $details,
            'name' => $name,
            'sizes' => $sizes,
            'stock' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'product_type_id' => $item_type_id,
            'genders' => $this->faker->randomElement(['1', '2', '1, 2']),
            'images' => json_encode([$this->faker->imageUrl(480, 480), $this->faker->imageUrl(480, 480), $this->faker->imageUrl(480, 480)]),
            'keywords' => $item_type_name . ', ' .
                $shop->name . ', ' .
                //$gender_name . ', ' .
                $sizes . ', ' .
                $name . ', ' .
                $details,
            'last_reposted' => Carbon::now(),
        ];
    }
}
