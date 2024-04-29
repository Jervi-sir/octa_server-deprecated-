<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friend>
 */
class FriendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure that you have enough users in your database to create friendships
        $userIds = User::pluck('id')->all();
        
        // Get two unique user IDs
        $userId = $this->faker->randomElement($userIds);
        $friendId = $this->faker->randomElement(array_diff($userIds, [$userId]));
        
        return [
            'user_id' => $userId,
            'friend_id' => $friendId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

}
