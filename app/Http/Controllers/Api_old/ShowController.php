<?php

namespace App\Http\Controllers\Api_old;

use App\Models\Item;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Friend;
use App\Models\ProductType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowController extends Controller
{
    public function showShop(Request $request, $shopId, $category_name = null)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $shop = Shop::find($shopId);
        $data['shop'] = OP_getShop($shop);

        if ($category_name !== null) {
            $category_id = ProductType::where('name', 'like', $category_name)->first()->id;
            $items = $shop->rls_items()->where('product_type_id', $category_id)->orderBy('id', 'DESC')->paginate(7);
            foreach ($items as $index => $item) {
                $data['items'][$index] = getItem($item);
            }
            
            $nextPage = null;
            if ($items->nextPageUrl()) {
                $nextPage = $items->currentPage() + 1;
            }
            
            return response()->json([
                //'user_status' => auth()->user() ? 'You are authenticated' : 'You are NOT authenticated',
                //'shop' => $data['shop'],
                'total' => $items->total(),
                'next_page' => $nextPage,
                /*'pagination' => [
                    'per_page' => $items->perPage(),
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                ],*/
                'items' => $data['items'],
            ], 200);
        }

        return response()->json([
            'shop' => $data['shop'],
        ], 200);
    }

    public function showUser(Request $request, $userId)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $auth = auth()->user();

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $isFriend = false;
        if($auth) {
            $isFriend = Friend::where(function($query) use ($auth, $userId) {
                $query->where('user_id', $auth->id)->where('friend_id', $userId);
            })->orWhere(function($query) use ($auth, $userId) {
                $query->where('user_id', $userId)->where('friend_id', $auth->id);
            })->exists();
        }

        $data['user'] = array_merge(OP_getProfile($user), [
            'bio' => $user->bio,
            'contacts' => $user->contacts,
            'nb_likes' => 0, //UserLike::where('liked_user_id', $userId)->count(),
            'nb_friends' => $user->rls_friends()->count(),
            'isPremium' => $user->isPremium,
        ]);

        $collections = Collection::where('collections.user_id', $userId)
            ->leftJoin('shop_collections', 'collections.id', '=', 'shop_collections.collection_id')
            ->withCount('shops')
            ->select('collections.*', DB::raw('MAX(shop_collections.updated_at) as last_shop_added_at'))
            ->groupBy('collections.id', 'collections.user_id', 'collections.name', 'collections.thumbnail', 'collections.created_at', 'collections.updated_at')
            ->orderBy('last_shop_added_at', 'desc')
            ->paginate(10);
           
        $data['collections'] = [];

        foreach ($collections as $index => $collection) {
            
            $data['collections'][$index] = [
                'id' => $collection->id,
                'name' => $collection->name,
                'thumbnail' => $collection->thumbnail,
                'shops_count' => $collection->shops_count,
                'last_shop_added_at' => $collection->last_shop_added_at
            ];
        }

        $nextPage = null;
        if ($collections->nextPageUrl()) {
            $nextPage = $collections->currentPage() + 1;
        }


        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => $nextPage,
            'user' => $data['user'],
            'collections' => $data['collections'],
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
