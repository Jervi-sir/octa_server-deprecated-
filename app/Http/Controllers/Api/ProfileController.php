<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showShop($shopId)
    {
        $shop = Shop::find($shopId);
        $data['shop'] = getShop($shop);

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

    public function showMyProfile(Request $request) 
    {
        $user = Auth::user();
        $data['user'] = [
            'username' => $user->username,
            'name' => $user->name,
            'isPremium' => $user->isPremium,
            'bio' => $user->bio,
            'contacts' => $user->contacts,
            'nb_followers' => $user->nb_followers,
            'nb_likes' => $user->nb_likes,
            'profile_images' => imageUrl('users', $user->profile_images),
        ];

        return response()->json($data['user']);
    }

}
