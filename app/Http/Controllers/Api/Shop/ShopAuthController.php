<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ShopAuthController extends Controller
{

    public function validateToken(Request $request)
    {
        $shop = auth()->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Token is valid',
            'shop_auth_info' => getShopAuthDetails($shop)
        ]);
    }

    public function createShop(Request $request) {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'phone_number' => 'required|unique:shops,phone_number',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $shop = Shop::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'access_token' => $shop->createToken($request->header('User-Agent'), ['role:shop'])->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function loginShop(Request $request) {

        try {
            $validateUser = Validator::make($request->all(), [
                'phone_number' => 'required',
                'password' => 'required'
            ]);
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            //if(!Auth::attempt($request->only(['phone_number', 'password']))){
            //    return response()->json([
            //        'status' => false,
            //        'message' => 'Phone Number & Password does not match with our record.',
            //    ], 401);
            //}

            $shop = Shop::where('phone_number', $request->phone_number)->first();

            if (!$shop || !Hash::check($request->password, $shop->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Auth Error',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'access_token' => $shop->createToken($request->header('User-Agent'), ['role:shop'])->plainTextToken,
                'shop_auth_info' => getShopAuthDetails($shop)
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logoutShop(Request $request) {
        //$request->user()->tokens()->delete();
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
