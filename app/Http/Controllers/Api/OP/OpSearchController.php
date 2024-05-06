<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Friend;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'keywords'   => 'nullable',
            'category_name'   => 'nullable',
            'gender_name'   => 'nullable',
            'wilaya_code'   => 'nullable',
            'page'   => 'nullable',
            'per_page' => 'nullable',
        ]);

        $per_page = $request->has('per_page') ? $request->per_page : 10;

        $auth = auth()->user();
        $data = $request->all();
        $items = Item::query();
    
        // Only apply keyword filtering if keywords are provided
        $keyword_array = !empty($data['keywords']) ? explode(",", $data['keywords']) : [];
        if (!empty($keyword_array)) {
            $items = $items->where(function ($query) use ($keyword_array) {
                foreach ($keyword_array as $keyword) {
                    if ($keyword) {
                        $query->where('keywords', 'like', '%' . $keyword . '%');
                    }
                }
            });
        }

        if (!empty($data['category_name'])) {
            $category_id = ItemType::where('name', 'like', '%' . $data['category_name'] . '%')->first()->id;
            $items = $items->where('item_type_id', $category_id);
        }

        if (!empty($data['gender_name'])) {
            if(strtolower($data['gender_name']) === 'both') {
                // If "both", include items tagged explicitly with "male,female"
                $items = $items->where('genders', 'male,female');
            } else {
                // Otherwise, search for items that contain the gender as a whole word within the string
                $items = $items->where('genders', 'like', $data['gender_name'] . '%');
            }
        }
    
        if (!empty($data['wilaya_code'])) {
            // Assuming you have a way to relate items with wilaya_code
            $items = $items->where('wilaya_code', $data['wilaya_code']);
        }

        $items = $items->orderBy('id', 'DESC')->paginate($per_page);
        $totalItems = $items->total();
        
        $data['items'] = [];
        foreach ($items as $index => $item) {
            $data['items'][$index] = OP_getItem($item);
        }

        $nextPage = null;
        if ($items->nextPageUrl()) {
            $nextPage = $items->currentPage() + 1;
        }

        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $items->nextPageUrl(),
            'next_page' => $nextPage,
            'total_items' => $totalItems,
            'last' => $items->lastPage(),
            'items' => $data['items'],
        ], 200);
    }

    public function searchShop(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
            'keywords'   => 'nullable',
            'wilaya_code'   => 'nullable',
        ]);

        $auth = auth()->user();

        $data = $request->all();
        $keyword_array = !empty($data['keywords']) ? explode(",", $data['keywords']) : [];
        $shops = Shop::query();
        
        if (!empty($keyword_array)) {
            $shops = $shops->where(function ($query) use ($keyword_array) {
                foreach ($keyword_array as $keyword) {
                    if ($keyword) {
                        // Search in both bio and username
                        $query->where('bio', 'like', '%' . $keyword . '%')
                              ->orWhere('shop_name', 'like', '%' . $keyword . '%');
                    }
                }
            });
        }
        if(!empty($data['wilaya_code'])) {
            $shops = $shops->where('wilaya_code', $data['wilaya_code']);
        }
        $shops = $shops->orderBy('id', 'DESC')->paginate(10);
    
        $data['shops'] = [];
        
        foreach ($shops as $index => $shop) {
            $data['shops'][$index] = OP_getShop($shop);
        }

        $nextPage = null;
        if ($shops->nextPageUrl()) {
            $nextPage = $shops->currentPage() + 1;
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => $nextPage,
            'shops' => $data['shops'],
        ], 200);
    }


    public function searchProfile(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'keywords' => 'nullable',
            'wilaya_code' => 'nullable',
        ]);
    
        $auth = auth()->user();
    
        $data = $request->all();
        $keyword_array = !empty($data['keywords']) ? explode(",", $data['keywords']) : [];
        $users = User::query();
    
        // Only get users who have a username
        $users = $users->whereNotNull('username');
    
        if (!empty($keyword_array)) {
            $users = $users->where(function ($query) use ($keyword_array) {
                foreach ($keyword_array as $keyword) {
                    if ($keyword) {
                        $query->where('username', 'like', '%' . $keyword . '%');
                    }
                }
            });
        }
        if(!empty($data['wilaya_code'])) {
            $users = $users->where('wilaya_code', $data['wilaya_code']);
        }
        $users = $users->orderBy('id', 'DESC')->paginate(10);
    
        $data['users'] = [];
        
        foreach ($users as $index => $user) {
            $data['users'][$index] = OP_getProfile($user);
        }
    
        $nextPage = null;
        if ($users->nextPageUrl()) {
            $nextPage = $users->currentPage() + 1;
        }
    
        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => $nextPage,
            'users' => $data['users'],
        ], 200);
    }

    public function showShop(Request $request, $shopId, $category_name = null)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $shop = Shop::find($shopId);
        $data['shop'] = OP_getShop($shop);
        $data['items'] = [];
        if ($category_name !== null) {
            $category_id = ItemType::where('name', 'like', $category_name)->first()->id;
            $items = $shop->rls_items()->where('item_type_id', $category_id)->orderBy('id', 'DESC')->paginate(7);
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
                'shop' => $data['shop'],
                'items' => $data['items'],
            ], 200);
        }

        return response()->json([
            'shop' => $data['shop'],
        ], 200);
    }

    public function showProfile(Request $request, $userId)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $auth = auth()->user();

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        //$isFriend = false;
        // if($auth) {
        //     $isFriend = Friend::where(function($query) use ($auth, $userId) {
        //         $query->where('user_id', $auth->id)->where('friend_id', $userId);
        //     })->orWhere(function($query) use ($auth, $userId) {
        //         $query->where('user_id', $userId)->where('friend_id', $auth->id);
        //     })->exists();
        // }

        $data['user'] = OP_getProfile($user);

        /*
        $collections = Collection::where('collections.user_id', $userId)
            ->leftJoin('shop_collections', 'collections.id', '=', 'shop_collections.collection_id')
            ->withCount('rls_shops')
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
        */

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => null, //$nextPage,
            'user' => $data['user'],
            'collections' => [] //$data['collections'],
        ], 200);
    }



    public function getSavedItems(Request $request)
    {
        $request->validate([
            'keywords'   => 'nullable',
            'category_name'   => 'nullable',
            'gender_name'   => 'nullable',
            'wilaya_code'   => 'nullable',
            'page'   => 'nullable',
            'per_page' => 'nullable',
        ]);
        $per_page = $request->has('per_page') ? $request->per_page : 10;

        $data = $request->all();
        $auth = auth()->user();

        $items = $auth->rls_saveItem();
      
        $keyword_array = !empty($data['keywords']) ? explode(",", $data['keywords']) : [];
        if (!empty($keyword_array)) {
            $items = $items->where(function ($query) use ($keyword_array) {
                foreach ($keyword_array as $keyword) {
                    if ($keyword) {
                        $query->where('keywords', 'like', '%' . $keyword . '%');
                    }
                }
            });
        }
        if (!empty($data['category_name'])) {
            $category_id = ItemType::where('name', 'like', '%' . $data['category_name'] . '%')->first()->id;
            $items = $items->where('item_type_id', $category_id);
        }
        if (!empty($data['gender_name'])) {
            if(strtolower($data['gender_name']) === 'both') {
                // If "both", include items tagged explicitly with "male,female"
                $items = $items->where('genders', 'male,female');
            } else {
                // Otherwise, search for items that contain the gender as a whole word within the string
                $items = $items->where('genders', 'like', $data['gender_name'] . '%');
            }
        }
        if (!empty($data['wilaya_code'])) {
            // Assuming you have a way to relate items with wilaya_code
            $items = $items->where('wilaya_code', $data['wilaya_code']);
        }

        $items = $items->orderBy('id', 'DESC')->paginate($per_page);
        $totalItems = $items->total();
        
        $data['items'] = [];
        foreach ($items as $index => $item) {
            $data['items'][$index] = OP_getItem($item);
        }
        $nextPage = null;
        if ($items->nextPageUrl()) {
            $nextPage = $items->currentPage() + 1;
        }
       
        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $items->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $items->lastPage(),
            'total_items' => $totalItems,
            'items' => $data['items'],
        ], 200);
    }
    
}
