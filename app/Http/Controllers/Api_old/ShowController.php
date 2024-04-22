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
