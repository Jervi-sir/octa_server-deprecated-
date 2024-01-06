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
      $user = auth()->user();
      $shop = $user->shop;

      $data['shop'] = [
        'phone_number' => $shop->phone_number,
        'email' => $shop->email,
        'credit' => $user->credit,
        'shop_name' => $shop->shop_name,
        'shop_image' => $shop->shop_image,
        'details' => $shop->details,
        'contacts' => $user->contacts,
        'location' => $shop->location,
        'map_location' => $shop->map_location,
        'nb_followers' => $shop->nb_followers,
        'threeD_model' => $shop->threeD_model,
        'wilaya_name' => $shop->wilaya_name,
        'wilaya_id' => $shop->wilaya_id,
        'user_id' => $shop->user_id,
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




