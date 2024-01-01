<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSave>
 */
class UserShopFollowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do {
            $user_id = User::inRandomOrder()->first()->id;
            $shop_id = Shop::inRandomOrder()->first()->id;
    
            $isUnique = !DB::table('user_shop_followings')->where('user_id', $user_id)->where('shop_id', $shop_id)->exists();
        } while (!$isUnique);
    
        return [
            'user_id' => $user_id,
            'shop_id' => $shop_id,
        ];
    }
}
