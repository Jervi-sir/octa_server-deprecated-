<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wilaya;
use Illuminate\Support\Facades\Storage;

class ShopProfileController extends Controller
{
    public function paymentHistory(Request $request)
    {
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


    public function updatePic_Name(Request $request)
    {
        $request->validate([
            'shop_name' => 'required',
            'base64_image' => 'required',
        ]);

        $data = $request->all();

        $shop = auth()->user();
        $shop->shop_name = $data['shop_name'];
        /*
        if ($data['base64_image'] !== null) {
            $imagePath = 'public/images/' . uniqid() . '.png';
            Storage::put($imagePath, base64_decode($data['base64_image']));
            $shop->shop_image = env('API_URL') . Storage::url($imagePath);
        }
        */
        $shop->save();

        //$newest_user = User::find($user->id);

        return response()->json([
            'success' => true,
            'shop_auth_info' => getMyShop()
        ]);

    }

    public function updateBio(Request $request)
    {

        $request->validate([
            'details' => 'required',
        ]);

        $data = $request->all();

        $shop = auth()->user();
        $shop->bio = $data['details'];
        $shop->save();

        return response()->json([
            'success' => true,
            'shop_auth_info' => getMyShop()
        ]);

    }


    public function updateSocialList(Request $request)
    {
        $request->validate([
            'social_media_list' => 'required',
        ]);

        $data = $request->all();

        $user = auth()->user();
        $user->contacts = json_encode($data['social_media_list']);
        $user->save();

        return response()->json([
            'status' => 'success',
            'shop_auth_info' => getMyShop()
        ]);
    }

    public function updateThisSocial(Request $request) {
        $validated = $request->validate([
            'id' => 'required|integer',
            'platform' => 'required|string',
            'profileURL' => 'required|string',
        ]);
    
        $shop = auth()->user();
        $contacts = json_encode($shop->contacts);
    
        return response()->json(['message' => ($contacts)]);
    
        // Get individual request data
        $requestedId = (int) $request->input('id');
        $requestedPlatform = $request->input('platform');
        $requestedProfileURL = $request->input('profileURL');
    
        $found = false;
    
        foreach ($contacts as $key => $contact) {
            if ((int) $contact['id'] === $requestedId) { // Ensure both IDs are integers
                $contacts[$key]['platform'] = $requestedPlatform;
                $contacts[$key]['profileURL'] = $requestedProfileURL;
                $found = true;
                break;
            }
        }
    
        if (!$found) {
            return response()->json(['message' => 'Contact not found'], 404);
        }
    
        // Encode the updated array back to JSON
        $shop->contacts = json_encode($contacts);
        $shop->save(); // Save the shop model
    
        return response()->json(['message' => 'Contact updated successfully', 'contacts' => $contacts]);
    }

    public function updateLocation(Request $request)
    {

        $request->validate([
            'location' => 'required',
            'wilaya_id' => 'required',
        ]);

        $data = $request->all();

        $shop = auth()->user();
        $shop->map_location = $data['location'];
        $shop->wilaya_code = $data['wilaya_id'];
        $shop->wilaya_name = Wilaya::where('code', $data['wilaya_id'])->first()->name;

        $shop->save();


        return response()->json([
            'success' => true,
            'shop_auth_info' => getMyShop()
        ]);

    }

    public function addSocials(Request $request)
    {
        $request->validate([
            'profileURL' => 'required',
            'platform' => 'required',
        ]);

        $shop = auth()->user();

        $contacts = json_decode($shop->contacts, true) ?? [];

        $contacts[] = [
            'platform' => $request->platform,
            'profileURL' => $request->profileURL,
        ];

        $shop->contacts = json_encode($contacts);
        $shop->save();

        return response()->json([
            'message' => 'Contact added successfully!',
            'shop_auth_info' => getMyShop()
        ]);
    }


    public function updateShopDetails(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'bio' => 'sometimes|string',
            'shop_image' => 'sometimes|string',
            'shop_name' => 'sometimes|string',
            'contact_number' => 'sometimes|numeric',
            'map_location' => 'sometimes|string',
        ]);
        
        $shop = auth()->user();
        
        // Check if the shop_image is present and process it
        if ($request->has('shop_image')) {
            //$uploadedFileUrl = $this->uploadImageToCloudinary($request->file('shop_image'));
            //$request->merge(['shop_image' => $uploadedFileUrl]);
            
            $imagePath = 'public/images/' . uniqid() . '.png';
            Storage::put($imagePath, base64_decode($request->shop_image));
            $shop->shop_image = env('API_URL') . Storage::url($imagePath);
        }

        // Fill and save the model with the request data
        $shop->fill($request->only(['bio', 'shop_name', 'contact_number', 'map_location']));
        $shop->save();

        return response()->json([
            'success' => true,
            'shop_auth_info' => $shop
        ]);
    }

    /*
    protected function uploadImageToCloudinary($image)
    {
        // Assuming you have set up Cloudinary integration
        \Cloudinary\Uploader::upload($image->getRealPath(), [
            'folder' => 'shop_images',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);
        return \Cloudinary\Uploader::getLastResponse()['secure_url'];
    }
    */

}
