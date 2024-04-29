<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLike>
 */
class UserLikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::pluck('id')->all();
        
        // Assign liker and liked ensuring they are not the same
        do {
            $likerId = $this->faker->randomElement($userIds);
            $likedId = $this->faker->randomElement($userIds);
        } while ($likerId === $likedId);

        return [
            'liker_id' => $likerId,
            'liked_id' => $likedId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
