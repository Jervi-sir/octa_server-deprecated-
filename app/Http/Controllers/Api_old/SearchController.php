<?php

namespace App\Http\Controllers\Api_old;

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
