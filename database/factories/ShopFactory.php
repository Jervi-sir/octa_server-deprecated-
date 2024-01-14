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

        //$user = User::inRandomOrder()->first();
        //$user->role_id = Role::where('role_name', 'shop')->first()->id;
        //$user->save();

        return [
            'username' => $this->faker->userName(),
            'phone_number' => $this->faker->phoneNumber,
            'password' => bcrypt('password'),
            'password_plainText' => 'password',

            'shop_name' => $this->faker->company,
            'shop_image' => $this->faker->imageUrl(480, 480),
            'bio' => $this->faker->paragraph(),
            'contacts' => json_encode([
                "phone" => $this->faker->phoneNumber,
                "facebook" => 'facebook',
                "instagram" => 'instagram',
            ]),
            
            'wilaya_code' => $wilaya->code, 
            'wilaya_name' => $wilaya->name, 
            'map_location' => $this->faker->latitude() . ',' . $this->faker->longitude(),

            'nb_followers' => $this->faker->numberBetween(0, 1000),
            'nb_likes' => $this->faker->numberBetween(0, 1000),

            'wilaya_created_at' => '46',
            
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_number_verified_at' => null,
        ]);
    }
}
