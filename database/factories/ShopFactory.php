<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
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

        $user = User::inRandomOrder()->first();
        $user->role_id = Role::where('role_name', 'shop')->first()->id;
        $user->save();

        return [
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'password_plainText' => 'password',
            'shop_name' => $this->faker->company,
            'shop_image' => $this->faker->imageUrl(480, 480),
            'details' => $this->faker->paragraph(),
            'contacts' => json_encode([
                "phone" => $this->faker->phoneNumber,
            ]),
            'location' => $this->faker->state,
            'map_location' => $this->faker->latitude() . ',' . $this->faker->longitude(),
            'nb_followers' => $this->faker->numberBetween(0, 1000),
            'nb_likes' => $this->faker->numberBetween(0, 1000),
            'threeD_model' => $this->faker->url,
            'wilaya_name' => $wilaya->name,
            'wilaya_id' => $wilaya->code,
            'user_id' => $user->id,
            
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
