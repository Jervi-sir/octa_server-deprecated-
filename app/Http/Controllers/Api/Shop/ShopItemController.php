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
            'product_type_id'   => 'required|numeric',
            'wilaya_number'     => 'nullable|string',
            'details'   => 'nullable|string',
            'name'      => 'nullable|string',
            'sizes'     => 'nullable|string',
            'stock'     => 'nullable|numeric',
            'price'     => 'nullable|numeric',
            'genders'   => 'nullable|string',
            'images'    => 'required|string',
        ]);

        $data = $request->all();
        $user = auth()->user();
        $shop = $user->shop;

        $item = new Item;
        $item->shop_id  = $shop->id;
        $item->user_id  = $user->id;
        $item->details  = $data['details']  ?? null; // Or you can set this as needed
        $item->name     = $data['name']     ?? 'Untitled';
        $item->sizes    = $data['sizes']    ?? null; // default
        $item->stock    = $data['stock']    ?? 1; // default
        $item->price    = $data['price']    ?? null;
        $item->genders  = $data['genders']  ?? null;
        $item->images   = $data['images'];
        $item->product_type_id =  $data['product_type_id']; // You'll need to map this to an actual ID
        $item->save();

        //$item->price = str_replace(' ', '', $data['productPrice']);
        //$item->genders  =   $data['selectedGenders'];//getGenderId($data['selectedGenders']);
        //$item->sizes = json_encode($data['selectedSizes']);
        // Save base64 images as files and store their paths
        /*
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
        */

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
        
    }

    public function repostItem(Request $request) {
        $request->validate([
            'item_id'   => 'required|numeric',
        ]);
        
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

    public function editItem(Request $request, $item_id) 
    {
        $user = auth()->user();
        $shop = $user->shop;
        $item = $shop->items->find($item_id);
        
        $data['item'] = [
            'id' => $item->id,
            'product_type_id' => $item->product_type_id,
            'wilaya_id' => $item->wilaya_id,

            'name' => $item->name,
            'details' => $item->details,
            'sizes' => $item->sizes,
            'stock' => $item->stock,
            'price' => $item->price,
            'product_type' => ProductType::find($item->product_type_id)->name,
            'genders' => $item->genders,
            'images' => json_decode($item->images),
            'keywords' => $item->keywords,
            'isActive' => $item->isActive,
            'last_reposted' => $item->last_reposted,
        ];

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
            'price'     => 'nullable|numeric',
            'genders'   => 'nullable|string',
            'base64Images'    => 'required',
        ]);

        $data = $request->all();

        $user = auth()->user();
        $shop = $user->shop;
        $item = $shop->items->find($item_id);

        $item->shop_id  = $shop->id;
        $item->user_id  = $user->id;
        $item->details  = $data['details']  ?? null; // Or you can set this as needed
        $item->name     = $data['name']     ?? 'Untitled';
        $item->price    = $data['price']    ?? null;
        $item->genders  = $data['genders']  ?? null;
        $item->images   = $data['base64Images'];
        $item->product_type_id =  ProductType::where('name', 'like', $data['product_type'])->first()->id; // You'll need to map this to an actual ID
        $item->product_type =  $data['product_type']; // You'll need to map this to an actual ID
        
        $imagePaths = [];
        foreach ($data['base64Images'] as $base64Image) {
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

        $item->save();

        return response()->json([
            'success'   => true,
            'item'      => $item,
        ]);
    }

    public function deleteItem(Request $request, $item_id) {
        $data = $request->all();

        $user = auth()->user();
        $shop = $user->shop;
        $item =  $shop->items->find($item_id);
        $item->delete();

        return response()->json([
            'success' => true,
            'item' => 200,
        ], 200);
    }
}
