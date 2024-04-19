<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Wilaya;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
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
            //'role_id' => Role::inRandomOrder()->first()->id, //assuming you have 3 roles in your roles table
            
            'phone_number' => json_encode(['phone' => $this->faker->phoneNumber(), 'address' => $this->faker->address()]),
            'password' => bcrypt('password'),
            'password_plainText' => 'password',
            'username' => $this->faker->userName(),
            'bio' => $this->faker->sentence(10),
            
            'profile_images' => [$this->faker->imageUrl(480, 480)],
            'contacts' => json_encode(['phone' => $this->faker->phoneNumber(), 'address' => $this->faker->address()]),
            'nb_likes' => $this->faker->numberBetween(0, 1000),
            'nb_friends' => $this->faker->numberBetween(0, 10000),
            'isPremium' => $this->faker->boolean(),

            'wilaya_id' => 46,

            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_number_verified_at' => null,
        ]);
    }
}
