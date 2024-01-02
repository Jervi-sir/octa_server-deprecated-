<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ShopProfileController extends Controller
{
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


    public function updatePic_Name(Request $request) {
        $request->validate([
            'shop_name'   => 'required',
            'base64_image' => 'required',
        ]);
        
        $data = $request->all();

        $shop= auth()->user()->shop;
        $shop->shop_name = $data['shop_name'];
        if ($data['base64_image'] !== null) {
            $imagePath = 'public/images/' . uniqid() . '.png';
            Storage::put($imagePath, base64_decode($data['base64_image']));
            $shop->shop_image = env('API_URL') . Storage::url($imagePath);
        }
        $shop->save();

        return response()->json([
            'success' => true,
            'shop_auth_info' => getShopAuthDetails($shop)
        ]);

    }

    public function updateBio(Request $request) {

        $request->validate([
            'description'   => 'required',
        ]);

        $data = $request->all();

        $shop = auth()->user()->shop;
        $shop->details = $data['description'];
        $shop->save();

        return response()->json([
            'success' => true,
            'shop_auth_info' => getShopAuthDetails($shop)
        ]);

    }

    public function updateSocialList(Request $request) {
        $request->validate([
            'social_media_list'   => 'required',
        ]);

        $data = $request->all();

        $shop_edit = auth()->user()->shop;
        $shop_edit->contacts = $data['social_media_list'];
        
        $shop_edit->save();


        return response()->json([
            'status' => 'success', 
            'message' => 'Token is valid',
            'shop_auth_info' => $data['socialMediaList']
        ]);
    }

    public function updateLocation(Request $request) {

        $request->validate([
            'location'   => 'required',
        ]);


        $data = $request->all();

        $shop = auth()->user()->shop;
        $shop->location = $data['location'];
        
        $shop->save();

        return response()->json([
            'success' => true,
            'shop_auth_info' => getShopAuthDetails($shop)
        ]);

    }

    
    
}
