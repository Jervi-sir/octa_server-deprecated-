<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Wilaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopUpdateProfileController extends Controller
{
    public function updateShopProfile(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'username' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'shop_name' => 'sometimes|string',
            'shop_image' => 'sometimes|string',
            'bio' => 'sometimes|string',
            'wilaya_id' => 'sometimes',
            'wilaya_name' => 'sometimes|string',
            'map_location' => 'sometimes|string',
        ]);
        
        $shop = auth()->user();
        
        // Check if the shop_image is present and process it
        if ($request->has('shop_image')) {
            $imagePath = 'public/images/' . uniqid() . '.png';
            Storage::put($imagePath, base64_decode($request->shop_image));
            $shop->shop_image = env('API_URL') . Storage::url($imagePath);
            //$shop->shop_image = $request->shop_image;
        }
        if($request->has('wilaya_name')) {
            $shop->wilaya_id = Wilaya::where('name', 'like', $request->wilaya_name)->first()->id;
        }

        // Fill and save the model with the request data
        $shop->fill($request->only([
            'username',
            'phone_number',
            'shop_name', 

            'bio', 
            'wilaya_id',
            'map_location'
        ]));
        $shop->save();

        return response()->json([
            'success' => true,
            'shop_auth_info' => $shop
        ]);
    }
}
