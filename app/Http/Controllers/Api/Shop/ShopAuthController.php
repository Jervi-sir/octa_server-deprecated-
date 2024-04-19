<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivationCode;
use App\Models\Shop;
use App\Models\Wilaya;
use App\Models\User;
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
            'shop_auth_info' => getMyShop()
        ]);
    }
    public function createShop(Request $request) {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required|unique:shops',
                'phone_number' => 'required|unique:shops',
                //'phone_number' => 'required|unique:shops,phone_number',
                'password' => 'required',
                'activation_code' => 'required',
                'wilaya_id' => 'sometimes',
                'wilaya_name' => 'sometimes',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if($request->has('wilaya_id')) { 
                $wilaya = Wilaya::where('code', $request->wilaya_id)->first();
            }
            if($request->has('wilaya_name')) { 
                $wilaya = Wilaya::where('name', 'like', $request->wilaya_name)->first();
            }
            
            $activationCode = ActivationCode::where('code', $request->activation_code)->first();

            if($activationCode === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'activation code does not exist',
                    'errors' => [
                        'activation_code' => ["Activation code does not exist"]
                    ]
                ], 401);
            }

            if($activationCode->isUsed === 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'activation code expired',
                    'errors' => [
                        'activation_code' => ["Activation code expired"]
                    ]
                ], 401);
            }

            $activationCode->isUsed = true;
            $activationCode->save();

            $shop = Shop::create([
                'username' => $request->username,
                'shop_name' => $request->username,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'password_plainText' => ($request->password),
                'wilaya_id' => $wilaya->id,
                'wilaya_created_at' => $wilaya->id,
                'activation_code_id' => $activationCode->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'access_token' => $shop->createToken($request->header('User-Agent'), ['auth:shops'])->plainTextToken,
                'shop_auth_info' => getMyShop($shop)
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
                'username' => 'required',
                'password' => 'required'
            ]);
            
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $shop = Shop::where('username', $request->username)->first();
            
            if (!$shop || !Hash::check($request->password, $shop->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Auth Error',
                ], 500);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'access_token' => $shop->createToken($request->header('User-Agent'), ['auth:shops'])->plainTextToken,
                'shop_auth_info' => getMyShop($shop)
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
