<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    public function showShop(Request $request, $shopId, $category_name = null)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $shop = Shop::find($shopId);
        $data['shop'] = getShop($shop);

        if ($category_name !== null) {
            $category_id = ProductType::where('name', 'like', $category_name)->first()->id;
            $items = $shop->items()->where('product_type_id', $category_id)->orderBy('id', 'DESC')->paginate(7);
            foreach ($items as $index => $item) {
                $data['items'][$index] = getItem($item);
            }
            return response()->json([
                //'user_status' => auth()->user() ? 'You are authenticated' : 'You are NOT authenticated',
                'shop' => $data['shop'],
                'pagination' => [
                    'total' => $items->total(),
                    'per_page' => $items->perPage(),
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                ],
                'items' => $data['items'],
            ], 200);
        }

        return response()->json([
            'shop' => $data['shop'],
        ], 200);
    }

    public function showUser(Request $request, $userId, $includeMap = false)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $auth = auth()->user();

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
            'isFollowing' => $auth ? $auth->isFollowedBy($userId) : null
        ];

        if ($includeMap) {
            $data['user']['game_map'] = $user->game_map;
        }
        
        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
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
