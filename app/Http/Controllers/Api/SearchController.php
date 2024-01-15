<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);
        $auth = auth()->user();
        $items = Item::inRandomOrder()->paginate(10);
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next' => $items->nextPageUrl(),
            'data' => $data['items'],
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
            $data['shops'][$index] = getShop($shop);
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

    public function search(Request $request)
    {
        $request->validate([
            'keywords'   => 'nullable',
            'category_name'   => 'nullable',
            'gender_name'   => 'nullable',
            'wilaya_code'   => 'nullable',
        ]);

        $auth = auth()->user();

        $data = $request->all();
        $keyword_array = !empty($data['keywords']) ? explode(",", $data['keywords']) : [];

        $items = Item::query();
    
        // Only apply keyword filtering if keywords are provided
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
            $category_id = ProductType::where('name', 'like', '%' . $data['category_name'] . '%')->first()->id;
            $items = $items->where('product_type_id', $category_id);
        }

        if (!empty($data['gender_name'])) {
            if(strtolower($data['gender_name']) != 'all')
            $items = $items->where('genders', 'like', '%' . $data['gender_name'] . '%');
        }
    
        if (!empty($data['wilaya_code'])) {
            // Assuming you have a way to relate items with wilaya_code
            $items = $items->where('wilaya_code', $data['wilaya_code']);
        }

        $items = $items->orderBy('id', 'DESC')->paginate(10);
        
        $data['items'] = [];
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        $nextPage = null;
        if ($items->nextPageUrl()) {
            $nextPage = $items->currentPage() + 1;
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $items->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $items->lastPage(),
            'items' => $data['items'],
        ], 200);
    }

    public function showItem(Request $request, $item_id) 
    {
        $auth = auth()->user();
        $item = Item::find($item_id);
        $data['item'] = getItem($item);

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'data' => $data['item'],
        ], 200);
    }

    public function byCategory(Request $request, $category_name)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $auth = auth()->user();
        $category_id = ProductType::where('name', 'like', $category_name)->first()->id;
        $items = Item::where('product_type_id', $category_id)->orderBy('id', 'DESC')->paginate(7);
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next' => $items->nextPageUrl(),
            'data' => $data['items'],
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
            $data['users'][$index] = getProfile($user);
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
    
}
