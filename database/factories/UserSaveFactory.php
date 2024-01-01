<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSave>
 */
class UserSaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        try {
            return [
                'user_id' => User::inRandomOrder()->first()->id,
                'item_id' => Item::inRandomOrder()->first()->id,
            ];
        } catch (\Throwable $th) {
            return null;
        }
    }
}
