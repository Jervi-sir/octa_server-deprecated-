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
        // An array of platforms you want to include
        $platforms = ['instagram', 'facebook', 'linkedin', 'youtube'];
        $faker = new Faker();

        // Create an array of contacts with random data for each platform
        $contacts = collect($platforms)->map(function ($platform, $index) use ($faker) {
            return [
                'id' => $index + 1,
                'platform' => $platform,
                'profileURL' => $this->faker->userName
            ];
        })->toArray();
        
        return [
            //'role_id' => Role::inRandomOrder()->first()->id, //assuming you have 3 roles in your roles table
            
            'phone_number' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'password_plainText' => 'password',

            'username' => $this->faker->unique()->userName,

            'bio' => 'Octa User',
            'profile_images' => $this->faker->imageUrl(480, 480),
            'contacts' => ($contacts),
            'nb_likes' => $this->faker->numberBetween(0, 100),
            'nb_friends' => $this->faker->numberBetween(0, 100),
            'isPremium' => $this->faker->boolean,
            'credit' => $this->faker->numberBetween(0, 500),
            'wilaya_id' => $this->faker->numberBetween(1, 58),
            'wilaya_created_at' => now()->toDateTimeString(),
    
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
