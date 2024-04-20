<?php

namespace App\Http\Controllers\Api\Shop;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Shop;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ShopProfileController extends Controller
{
    public function myStoreInfo(Request $request)
    {
      return response()->json([
        'success' => true,
        'shop' => OS_getMyShop(),
      ]);
    }

    public function showMyFollowers(Request $request)
    {
      $request->validate([
        'page'   => 'nullable',
      ]);
      $shop = auth()->user();

      $data['followers'] = [];
      $followers = $shop->rls_followers()->orderBy('id', 'desc')->paginate(2);
      foreach ($followers as $key => $follower) {
        $data['followers'][$key] = [
            'user_id' => $follower->id,
            'user' => OS_getUserAsShopgetUserAsShop($follower),
            'created_at' => $follower->created_at,
        ];
      }

      return response()->json([
        'success' => true,
        'pagination' => [
          'total' => $followers->total(),
          'per_page' => $followers->perPage(),
          'current_page' => $followers->currentPage(),
          'last_page' => $followers->lastPage(),
          'from' => $followers->firstItem(),
          'to' => $followers->lastItem(),
      ],
        'followers' => $data['followers'],
      ]);
    }
}
