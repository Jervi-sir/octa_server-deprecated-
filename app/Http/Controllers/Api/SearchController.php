<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function suggest()
    {
        $items = Item::inRandomOrder()->paginate(10);
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'next' => $items->nextPageUrl(),
            'data' => $data['items'],
        ], 200);
    }

    public function suggestShop()
    {
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
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'next' => $shops->nextPageUrl(),
            'shops' => $data['shops'],
        ], 200);
    }

    public function search($keywords)
    {
        $keyword_array = explode(",", $keywords);

        $items = Item::where(function ($query) use ($keyword_array) {
            foreach ($keyword_array as $keyword) {
                $query->where('keywords', 'like', '%' . $keyword . '%');
            }
        })->paginate(10);

        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'next' => $items->nextPageUrl(),
            'last' => $items->lastPage(),
            'shops' => $data['items'],
        ], 200);
    }
}
