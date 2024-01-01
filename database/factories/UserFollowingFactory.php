<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSave>
 */
class UserFollowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do {
            $follower_id = User::inRandomOrder()->first()->id;
            $following_id = User::where('id', '!=', $follower_id)->inRandomOrder()->first()->id;
            
            $isUnique = !DB::table('user_followings')->where('follower_id', $follower_id)->where('following_id', $following_id)->exists();
        } while (!$isUnique);
    
        return [
            'follower_id' => $follower_id,
            'following_id' => $following_id,
        ];
    }
}
