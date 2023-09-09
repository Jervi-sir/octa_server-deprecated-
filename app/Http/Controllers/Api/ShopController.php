<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Shop;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
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
                'access_token' => $shop->createToken($request->header('User-Agent'), ['role:shop'])->plainTextToken
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

    public function publishItem(Request $request) {

        $data = $request->all();
        $item = new Item;
        $item->shop_id = auth()->id();
        $item->details = $data['productDescription']; // Or you can set this as needed
        $item->name = $data['productName'];
        //$item->sizes = json_encode($data['selectedSizes']);
        $item->stock = 1; // default
        $item->price = str_replace(' ', '', $data['productPrice']);
        $item->product_type_id = ProductType::where('name', 'like', $data['productType'])->first()->id; // You'll need to map this to an actual ID
        $item->genders = getGenderId($data['selectedGenders']);
        
        // Save base64 images as files and store their paths
        $imagePaths = [];
        foreach ($data['base64Images'] as $base64Image) {
            if ($base64Image !== null) {
                $imagePath = 'public/images/' . uniqid() . '.png';
                Storage::put($imagePath, base64_decode($base64Image));
                $imagePaths[] = $imagePath;
            }
        }
        $item->images = json_encode($imagePaths);

        $item->save();

        return response()->json([
            'success' => true,
            'item' => '200',
        ]);
        
    }
    public function repostItem(Request $request) {
        
        $item = Item::find($request->item_id);
        
        $item->last_reposted = Carbon::now();
        $item->isActive = 1;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'updated successfully',
            'last_reposted' => $item->last_reposted,
        ]);
    }

    public function paymentHistory(Request $request) {
        $shop = auth()->user();
        $payment_histories = $shop->paymentHistory()->orderBy('id', 'desc')->paginate(7);
        $data['payment'] = [];

        foreach ($payment_histories as $index => $payment_history) {
            $data['payment'][$index] = [
                'id' => $payment_history->id,
                'user_username' => $payment_history->user->name,
                'amount' => $payment_history->amount,
                'sold_bought' => $payment_history->sold_bought,
                'created_at' => $payment_history->created_at,
            ];
        }
        return response()->json([
            'payment_history' => $data['payment'],
            'pagination' => [
                'total' => $payment_histories->total(),
                'per_page' => $payment_histories->perPage(),
                'current_page' => $payment_histories->currentPage(),
                'last_page' => $payment_histories->lastPage(),
                'from' => $payment_histories->firstItem(),
                'to' => $payment_histories->lastItem(),
            ]
        ]);
    }

    public function listMyProducts($category_name) {
        $shop = auth()->user();
        $category_id = ProductType::where('name', 'like', $category_name)->first()->id; // You'll need to map this to an actual ID
        //$products = $shop->items()->where('product_type_id', $category_id)->orderBy('id', 'desc')->paginate(7);
        $products = Item::where('product_type_id', $category_id)->orderBy('id', 'desc')->paginate(7);
        
        $data['products'] = [];

        foreach ($products as $index => $product) {
            $selected_image = json_decode($product->images)[0];
           // $image = strpos($selected_image, "https") !== false ? $selected_image : 'http://192.168.1.105:8000' . Storage::url($selected_image);
            
            $data['products'][$index] = [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail' => json_decode($product->images),
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
                'is_expired' => checkIfItemIsExpired($product->last_reposted),
                'last_reposted' => $product->last_reposted,
                'created_at' => $product->created_at
            ];
        }
        return response()->json([
            'products' => $data['products'],
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    public function listMyProductsWithOffset($category_name, $start_id = null) {
        $shop = auth()->user();
        $category_id = ProductType::where('name', 'like', $category_name)->first()->id;
    
        $products = Item::where('product_type_id', $category_id)->where('id', '<=', $start_id)->orderBy('id', 'desc')->paginate(7);
    
        $data['products'] = [];
    
        $currentPage = $products->currentPage(); // Capture the current page
    
        foreach ($products as $index => $product) {
            $selected_image = json_decode($product->images)[0];
    
            $data['products'][$index] = [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail' => json_decode($product->images),
                'price' => $product->price,
                'product_type_id' => $product->product_type_id,
                'current_page' => $currentPage,  // Include the current page for each product
                'is_expired' => checkIfItemIsExpired($product->last_reposted),
                'last_reposted' => $product->last_reposted,
                'created_at' => $product->created_at
            ];
        }
    
        return response()->json([
            'products' => $data['products'],
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }
    

}




