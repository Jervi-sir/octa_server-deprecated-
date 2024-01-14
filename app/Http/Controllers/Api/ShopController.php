<?php

namespace App\Http\Controllers\Api;

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

class ShopController extends Controller
{
    public function myStoreInfo(Request $request)
    {
      $shop = auth()->user();

      $data['shop'] = [
        'username' => $shop->username,
        'phone_number' => $shop->phone_number,
        'shop_name' => $shop->shop_name,
        'shop_image' => $shop->shop_image,
        'bio' => $shop->bio,
        'contacts' => $shop->contacts,
        'wilaya_code' => $shop->wilaya_code,
        'wilaya_name' => $shop->wilaya_name,
        'map_location' => $shop->map_location,
        'nb_followers' => $shop->nb_followers,
        'nb_likes' => $shop->nb_likes,
        'total_items' => $shop->items->count(),

        'created_at' => $shop->created_at,
      ];

      return response()->json([
        'success' => true,
        'shop' => $data['shop'],
      ]);
    }

    

    public function showMyFollowers(Request $request)
    {
      $request->validate([
        'page'   => 'nullable',
      ]);
      $user_shop = auth()->user();
      $shop = $user_shop->shop;

      $followers = $shop->userfollowers()->orderBy('id', 'desc')->paginate(2);
      foreach ($followers as $key => $follower) {
        $data['followers'][$key] = [
            'user_id' => $follower->id,
            'user_name' => $follower->name,
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




