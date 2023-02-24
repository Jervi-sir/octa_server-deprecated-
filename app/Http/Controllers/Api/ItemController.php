<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Item;

class ItemController extends Controller
{
    public function showShop($id) {
        try {
            $shop = Shop::find($id);

            //return $shop->items()->paginate(1);
            return response()->json([
                'status' => true,
                'shop' => $shop,
                'items' => $shop->items,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function showItem($id) {
        try {
            $item = Item::find($id);
            
            return $item;
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
