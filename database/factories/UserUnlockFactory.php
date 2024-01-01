<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMap>
 */
class UserUnlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = new Faker();
        $role_user_id = Role::where('role_name', 'user')->first()->id;
        
        return [
            'user_id' => User::where('role_id', $role_user_id)->inRandomOrder()->first()->id,
            'shop_id' => Shop::inRandomOrder()->first()->id,
        ];
    }
}
