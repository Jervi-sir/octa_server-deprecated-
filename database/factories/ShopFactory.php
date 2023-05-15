<?php

namespace Database\Factories;

use App\Models\Wilaya;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $faker = new Faker();
        $wilaya = Wilaya::inRandomOrder()->first();
        return [
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'shop_name' => $this->faker->company,
            'shop_image' => $this->faker->imageUrl(480, 480),
            'details' => $this->faker->paragraph(),
            'contacts' => $this->faker->phoneNumber,
            'location' => $this->faker->state,
            'map_location' => $this->faker->latitude() . ',' . $this->faker->longitude(),
            'nb_followers' => $this->faker->numberBetween(0, 1000),
            'nb_likes' => $this->faker->numberBetween(0, 1000),
            'threeD_model' => $this->faker->url,
            'wilaya_name' => $wilaya->name,
            'wilaya_id' => $wilaya->code,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
