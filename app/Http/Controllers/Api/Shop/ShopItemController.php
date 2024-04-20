<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ShopItemController extends Controller
{
    public function publishItem(Request $request)
    {
        $validateInput = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'price' => 'nullable|string',
            'item_type' => 'required|string',
            'details' => 'nullable|string',
            'genders' => 'nullable',
            'base64Images' => 'nullable',
        ]);
        if ($validateInput->fails()) {
            return response()->json(errorMessage($validateInput), 401);
        }

        $user = auth()->user();

        $shop = $user;

        $itemType = ItemType::where('name', 'like', $request->item_type)->first();

        $item = new Item;
        $item->shop_id = $shop->id;
        $item->item_type_id = $itemType->id; // You'll need to map this to an actual ID
        $item->name = $request->name ?? 'Untitled';
        $item->details = $request->details ?? null; // Or you can set this as needed
        $item->price = $request->price ?? null;
        $item->genders = ($request->genders) ?? null;
        $item->keywords = $request->name . ', ' . $request->details; // default
        $item->last_reposted = now();

        $imagePaths = [];
        foreach ($request->base64Images as $base64Image) {
            if ($base64Image !== null) {
                $imageName = uniqid() . '.png';
                $imagePath = 'public/images/' . $imageName;
                Storage::put($imagePath, base64_decode($base64Image));
                $imagePaths[] = '/storage/images/' . $imageName;        //env('API_URL') . 
            }
        }
        $item->images = json_encode($imagePaths);

        $item->save();

        return response()->json([
            'message' => 'Item added successfully',
            'item' => $item,
        ]);
    }

    public function repostItem(Request $request)
    {
        $validateInput = Validator::make($request->all(), [
            'item_id' => 'required|numeric',
        ]);
        if ($validateInput->fails()) {
            return response()->json(errorMessage($validateInput), 401);
        }

        $shop = auth()->user();

        $item = $shop->rls_items->find($request->item_id);
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

        return response()->json([
            'success' => true,
            'item' => OS_getProductAsShop($item),
        ]);

    }

    public function updateItem(Request $request, $item_id)
    {
        $validateInput = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'price' => 'nullable|string',
            'item_type' => 'required|string',
            'details' => 'nullable|string',
            'genders' => 'nullable',
            'base64Images' => 'nullable',       //this format will be an array of json [{uri: '', base64: '', isNew: true/false}]
        ]);
        if ($validateInput->fails()) {
            return response()->json(errorMessage($validateInput), 401);
        }


        $item_type = ItemType::where('name', 'like', $request->item_type)->first();

        $shop = auth()->user();
        $item = $shop->rls_items->find($item_id);

        $item->name = $request->name ?? 'Untitled';
        $item->details = $request->details ?? null; // Or you can set this as needed
        $item->price = $request->price ?? null;
        $item->genders = $request->genders ?? null;
        $item->keywords = $request->name . ', ' . $request->details; // default

        $item->images = $request->base64Images;

        $item->item_type_id = $item_type->id; // You'll need to map this to an actual ID
        /*to upload images correctly i will have to test if isNew false then push the uri, if true then upload the base64 and push new url */
        $imagePaths = [];
        foreach ($request->base64Images as $imageData) {
            if ($imageData !== null) {
                // If isNew is false, then push uri to the imagePaths array
                if (!$imageData['isNew']) {
                    // Use parse_url to extract the path component
                    $urlComponents = parse_url($imageData['uri']);
                    $pathOnly = $urlComponents['path'] ?? ''; // Default to empty string if not set
                    $imagePaths[] = $pathOnly;
                } else {
                    // If isNew is true, handle the base64 encoded image
                    if (!empty($imageData['base64'])) {
                        $imageName = uniqid() . '.png';
                        $imagePath = 'public/images/' . $imageName;
                        Storage::put($imagePath, base64_decode($imageData['base64']));
                        $imagePaths[] = '/storage/images/' . $imageName;
                    }
                }
            }
        }
        $item->images = json_encode($imagePaths);
        $item->save();

        return response()->json([
            'success' => true,
            'item' => OS_getProductAsShop($item),
        ]);
    }

    public function deleteItem(Request $request, $item_id)
    {
        $user = auth()->user();
        $shop = $user;
        $item = $shop->rls_items->find($item_id);
        $item->delete();

        return response()->json([
            'success' => true,
            'item' => 200,
        ], 200);
    }
}
