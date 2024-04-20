<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wilaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OpAuthController extends Controller
{
    public function validateToken(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Token is valid',
            'user_auth_info' => getMyProfile()
        ]);
    }

    public function createUser (Request $request) {
        try {
            $validateUser = Validator::make($request->all(), [
                'phone_number' => 'sometimes|unique:users',
                'email' => 'sometimes|unique:users',
                'password' => 'required',
                'username' => 'sometimes|unique:users',
                'wilaya_id' => 'sometimes',
                //'phone_number' => 'required|unique:shops,phone_number',
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
          
            $user = User::create([
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $request->username,
                'password_plainText' => ($request->password),
                'wilaya_id' => $wilaya->id,
                'wilaya_created_at' => $wilaya->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'access_token' => $user->createToken($request->header('User-Agent'), ['auth:users'])->plainTextToken,
                'shop_auth_info' => getMyProfile($user)
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request) 
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                //'phone_number' => 'required:users', //,phone_number
                'username' => 'required',
                'password' => 'required',
            ]);
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Auth Error',
                ], 500);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'access_token' => $user->createToken($request->header('User-Agent'))->plainTextToken,
                'my_account' => getMyProfile($user)
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logoutUser(Request $request) {
        
        //$request->user()->tokens()->delete();
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function verifyUsernameAvailability(Request $request)
    {
        $validateUser = Validator::make($request->all(), 
        [
            'username' => 'required|unique:users,username', //,phone_number
        ]);
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        return response()->json([
            'usernameExists' => false,
            'message' => 'username does not exist'
        ]);
    }
}
