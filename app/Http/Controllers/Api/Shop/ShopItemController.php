<?php

namespace App\Http\Controllers\Api\Shop;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ShopItemController extends Controller
{
    public function publishItem(Request $request) 
    {
        $request->validate([
            'name'      => 'nullable|string',
            'price'     => 'nullable|string',
            'product_type'   => 'required|string',
            'genders'   => 'nullable|array',
            'details'   => 'nullable|string',
            'base64Images'    => 'required|array',

            'wilaya_number'     => 'nullable|string',
            'sizes'     => 'nullable|string',
            'stock'     => 'nullable|numeric',
        ]);

        $data = $request->all();
        $user = auth()->user();
        
        $shop = $user;


        $productType = ProductType::where('name', 'like', $data['product_type'])->first();

        $item = new Item;
        $item->shop_id  = $shop->id;
        $item->product_type_id  =  $productType->id; // You'll need to map this to an actual ID
        $item->product_type     =  $productType->name; // You'll need to map this to an actual ID
        $item->name     = $data['name']     ?? 'Untitled';
        $item->details  = $data['details']  ?? null; // Or you can set this as needed
        $item->price    = $data['price']    ?? null;
        $item->genders  = json_encode($data['genders'])  ?? null;
        $item->keywords = $data['name'] . ', ' .  $data['details']; // default
        $item->wilaya_code  = $data['wilaya_number']  ?? $user->wilaya_code;
        $item->last_reposted = now();
        
        $imagePaths = [];
        foreach ($data['base64Images'] as $base64Image) {
            if ($base64Image !== null) {
                $imageName = uniqid() . '.png';
                $imagePath = 'public/images/' . $imageName;
                Storage::put($imagePath, base64_decode($base64Image));
                $imagePaths[] = env('API_URL') . '/storage/images/' . $imageName;
            }
        }

        $item->images = json_encode($imagePaths);
        $item->save();

        return response()->json([
            'message' => 'Item added successfully',
            'item' => $item,
        ]);
    }

    public function repostItem(Request $request) {
        $request->validate([
            'item_id'   => 'required|numeric',
        ]);
        
        $shop = auth()->user();
        
        $item =  $shop->rls_items->find($request->item_id);
        $item->last_reposted = Carbon::now();
        $item->isActive = 1;
        
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'updated successfully',
            'last_reposted' => $item->last_reposted,
        ]);
    }

    public function editItem(Request $request, $item_id) 
    {
        $user = auth()->user();
        $shop = $user;
        $item = $shop->rls_items->find($item_id);
        
        $data['item'] = getItem($item);

        return response()->json([
            'success' => true,
            'item' => $data['item'],
        ]);
        
    }

    public function updateItem(Request $request, $item_id) {

        
        $request->validate([
            'product_type'   => 'required',
            'details'   => 'nullable|string',
            'name'      => 'nullable|string',
            'price'     => 'nullable|string',
            'genders'   => 'nullable|string',
            'images'    => 'required|string',
        ]);
        
        $data = $request->all();

        $user = auth()->user();
        $shop = $user;
        $item = $shop->rls_items->find($item_id);

        $item->product_type_id =  ProductType::where('name', 'like', $data['product_type'])->first()->id; // You'll need to map this to an actual ID
        $item->product_type =  $data['product_type']; // You'll need to map this to an actual ID

        $item->name     = $data['name']     ?? 'Untitled';
        $item->details  = $data['details']  ?? null; // Or you can set this as needed
        $item->price    = $data['price']    ?? null;
        $item->genders  = $data['genders']  ?? null;
        $item->keywords = $data['name'] . ', ' .  $data['details']; // default

        $item->images   = $data['images'];
        
        /*
        $imagePaths = [];
        foreach ($data['images'] as $base64Image) {
            if ($base64Image !== null) {
                // Check if the string is a URL
                if (filter_var($base64Image, FILTER_VALIDATE_URL)) {
                    $imagePaths[] = $base64Image;
                } else {
                    // Handle the base64 encoded image
                    $imageName = uniqid() . '.png';
                    $imagePath = 'public/images/' . $imageName;
                    Storage::put($imagePath, base64_decode($base64Image));
                    $imagePaths[] = env('API_URL') . '/storage/images/' . $imageName;
                }
            }
        }
        $item->images = json_encode($imagePaths);
        */
        $item->images = $request->images;

        $item->save();

        return response()->json([
            'success'   => true,
            'item'      => $item,
        ]);
    }

    public function deleteItem(Request $request, $item_id) {
        $data = $request->all();

        $user = auth()->user();
        $shop = $user;
        $item =  $shop->rls_items->find($item_id);
        $item->delete();

        return response()->json([
            'success' => true,
            'item' => 200,
        ], 200);
    }
}
