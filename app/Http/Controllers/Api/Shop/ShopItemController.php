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
                $imageName = uniqid() . '.png';
                $imagePath = 'public/images/' . $imageName;
                Storage::put($imagePath, base64_decode($base64Image));
                $imagePaths[] = env('API_URL') . '/storage/images/' . $imageName;
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

    public function editItem(Request $request, $item_id) {
        
        $shop = auth()->user();
        $item =  $shop->items->find($item_id);
        
        $data['item'] = [
            'id' => $item->id,
            'name' => $item->name,
            'details' => $item->details,
            'sizes' => $item->sizes,
            'stock' => $item->stock,
            'price' => $item->price,
            'product_type' => ProductType::find($item->product_type_id)->name,
            'genders' => getGenderNames($item->genders),
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

        $data = $request->all();
        
        $shop = auth()->user();
        $item =  $shop->items->find($item_id);

        $item->shop_id = $shop->id;
        $item->details = $data['productDescription']; // Or you can set this as needed
        $item->name = $data['productName'];
        //$item->sizes = json_encode($data['selectedSizes']);
        $item->stock = 1; // default
        $item->price = str_replace(' ', '', $data['productPrice']);
        $item->product_type_id = ProductType::where('name', 'like', $data['productType'])->first()->id; // You'll need to map this to an actual ID
        $item->genders = getGenderId($data['selectedGenders']);
       

        // Save base64 images as files and store their paths
        $imagePaths = [];
        foreach ($data['base64Images'] as $index => $image) {
            // Check if it's a URL or null, if so leave it as is
            if ($image === null || filter_var($image, FILTER_VALIDATE_URL)) {
                $imagePaths[$index] = $image;
            } 
            // Assume remaining as base64 encoded images
            else {
                $imageName = uniqid() . '.png';
                $imagePath = 'public/images/' . $imageName;
                //Storage::put($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image)));
                Storage::put($imagePath, base64_decode($image));
                $imagePaths[$index] = env('API_URL') . '/storage/images/' . $imageName;
            }
        }
        $item->images = json_encode(removeNullsFromStart($imagePaths));
       
        $item->save();

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
        
    }

    public function deleteItem(Request $request, $item_id) {
        $data = $request->all();
        
        $shop = auth()->user();
        $item =  $shop->items->find($item_id);
        $item->delete();

        return response()->json([
            'success' => true,
            'item' => 200,
        ], 200);
        
    }
}
