<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\ProductType;
use App\Models\Shop;
use App\Models\Wilaya;
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
        $details = $this->faker->paragraph;
        $name = $this->faker->words(3, true);
        $sizes = array_rand(['Small', 'Medium', 'Large']);
        $item_type_id = $this->faker->numberBetween(1, 4);
        $item_type_name = $types[$item_type_id];
        
        $gender_name = $this->faker->randomElement(['_male', '_female', '_male/_female']);

        $shop = Shop::inRandomOrder()->first();
        $user = $shop->user;
        $wilaya = Wilaya::inRandomOrder()->first();
        $product_type = ProductType::inRandomOrder()->first();

        return [
            'shop_id' => $shop->id,
            'product_type_id' => $product_type->id,
            'product_type' => $product_type->name,

            'name' => $name,
            'details' => $details,
            'price' => $this->faker->randomFloat(2, 5, 100),

            'genders' => $gender_name,
            'images' => json_encode([$this->faker->imageUrl(480, 480), $this->faker->imageUrl(480, 480), $this->faker->imageUrl(480, 480)]),
            'keywords' => $item_type_name . ', ' .
                $shop->name . ', ' .
                $gender_name . ', ' .
                $sizes . ', ' .
                $name . ', ' .
                $details,
               
            'wilaya_code' => $wilaya->code,
            'isActive' => $this->faker->numberBetween(0, 1),
            'last_reposted' => Carbon::now(),
        ];
    }
}
