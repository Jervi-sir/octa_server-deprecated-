<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function semiCreateUser(Request $request) {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'phone_number' => 'required|unique:users,phone_number',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = new User();
            //$user->role_id = Role::where('role_name', 'user')->first()->id;
            $user->phone_number = $request->phone_number;
            $user->password = Hash::make($request->password);
            $user->password_plainText = ($request->password);

            $user->save();
            
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'access_token' => $user->createToken($request->header('User-Agent'))->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function completeCreateUser(Request $request) {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'wilaya_code' => 'nullable',
                'username' => 'required|unique:users,username',
            ]);


            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $user = auth()->user();
            //$user->role_id = Role::where('role_name', 'user')->first()->id;
            $user->username = $request->username;
            $user->wilaya_code = $request->wilaya_code;
            $user->save();
            
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'user' => $user,
                'my_account' => getMyProfile($user)
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
                'phone_number' => 'required:users,phone_number',
                'password' => 'required'
            ]);
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('phone_number', $request->phone_number)->first();

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



}
