<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductType;
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

    public function suggestShop()
    {
        $auth = auth()->user();
        $shops = Shop::inRandomOrder()->paginate(10);
        foreach ($shops as $index => $shop) {
            $data['shops'][$index] = [
                'id' => $shop->id,
                'shop_name' => $shop->shop_name,
                'shop_image' => $shop->shop_image,
                //'shop_image' => imageUrl('shops', $shop->shop_image),
                'map_location' => $shop->map_location,
            ];
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next' => $shops->nextPageUrl(),
            'shops' => $data['shops'],
        ], 200);
    }

    public function search(Request $request)
    {
        $request->validate([
            'keywords'   => 'nullable',
        ]);

        $auth = auth()->user();
        $data = $request->all();
        $keyword_array = explode(",", $data['keywords']);
        $items = Item::where(function ($query) use ($keyword_array) {
            foreach ($keyword_array as $keyword) {
                $query->where('keywords', 'like', '%' . $keyword . '%');
            }
        })->paginate(10);

        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $items->nextPageUrl(),
            'last' => $items->lastPage(),
            'shops' => $data['items'],
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
}
