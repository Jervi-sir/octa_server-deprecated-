<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    public function showShop($shopId)
    {
        $shop = Shop::find($shopId);
        $data['shop'] = getShop($shop);

        $items = $shop->items;
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }
        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'shop' => $data['shop'],
            'items' => $data['items'],
        ], 200);
    }

    public function showUser($userId)
    {
        $user = User::find($userId);
        $data['user'] = [
            'name' => $user->name,
            'username' => $user->username,
            'bio' => $user->bio,
            'profile_images' => $user->profile_images,
            'contacts' => $user->contacts,
            'nb_likes' => $user->nb_likes,
            'nb_followers' => $user->nb_followers,
            'isPremium' => $user->isPremium,
        ];
        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'user' => $data['user']
        ], 200);
    }

    public function showItem($id)
    {
        try {
            $item = Item::find($id);

            return response()->json([
                'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
                'item' => getItem($item),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
