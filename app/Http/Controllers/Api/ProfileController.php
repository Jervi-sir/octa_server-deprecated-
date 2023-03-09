<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function showShop($shopId)
    {
        $shop = Shop::find($shopId);
        $data['shop'] =[
            'shop_name' => $shop->shop_name,
            'details' => $shop->details,
            'contacts' => $shop->contacts,
            'location' => $shop->location,
            'map_location' => $shop->map_location,
            'nb_followers' => $shop->nb_followers,
            'nb_likes' => $shop->nb_likes,
            'wilaya_name' => $shop->wilaya_name,
        ];

        $items = $shop->items;
        foreach($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }
        return response()->json([
            'shop' => $data['shop'],
            'items' => $data['items'],
        ], 200);
    }

    public function showUser($userId)
    {
        $user = User::find($userId);
        $data['user'] =[
            'name' => $user->name,
            'username' => $user->username,
            'bio' => $user->bio,
            'profile_images' => $user->profile_images,
            'contacts' => $user->contacts,
            'nb_likes' => $user->nb_likes,
            'nb_followers' => $user->nb_followers,
            'isPremium' => $user->isPremium,
        ];
        return response()->json($data['user'], 200);
    }

}
