<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Shop;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest() {
        $items = Item::inRandomOrder()->paginate(10);
        foreach($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }
        
        return response()->json([
            'data' => $data['items'],
            'next' => $items->nextPageUrl(),
        ]);
    }

    public function suggestShop() {
        $shops = Shop::inRandomOrder()->paginate(10);
        foreach($shops as $index => $shop) {
            $data['shops'][$index] = [
                'id' => $shop->id,
                'shop_name' => $shop->shop_name,
                'shop_image' => imageUrl('shops', $shop->shop_image),
                'map_location' => $shop->map_location,
            ];
        }
        return response()->json([
            'shops' => $data['shops'],
        ]);
    }

    public function search($keywords) {
        $keyword_array = explode (",", $keywords); 

        $items = Item::where(function($query) use ($keyword_array) {
                foreach($keyword_array as $keyword) {
                    $query->where('search', 'like', '%' . $keyword . '%');
                }
            })
            ->paginate(10);

        foreach($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }
        
        return response()->json([
            'data' => $data['items'],
            'next' => $items->nextPageUrl(),
        ]);
    }

}
