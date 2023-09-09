<?php

namespace Database\Factories;

use App\Models\Role;
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
            'role_id' => Role::inRandomOrder()->first()->id, //assuming you have 3 roles in your roles table
            'phone_number' => json_encode(['phone' => $this->faker->phoneNumber(), 'address' => $this->faker->address()]),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'password_plainText' => 'password',
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'bio' => $this->faker->sentence(10),
            'profile_images' => $this->faker->imageUrl(480, 480),
            'contacts' => json_encode(['phone' => $this->faker->phoneNumber(), 'address' => $this->faker->address()]),
            'nb_likes' => $this->faker->numberBetween(0, 1000),
            'nb_followers' => $this->faker->numberBetween(0, 10000),
            'isPremium' => $this->faker->boolean(),
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
            'email_verified_at' => null,
        ]);
    }
}
