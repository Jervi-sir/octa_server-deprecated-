<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function createCollection(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'thumbnail' => 'nullable|string'
        ]);

        $user_id = Auth::id();

        $collection = new Collection();
        $collection->user_id = $user_id;
        $collection->name = $request->name;
        $collection->thumbnail = $request->thumbnail;
        $collection->save();

        $data['collection'] = [
            'id' => $collection->id,
            'name' => $collection->name,
            'thumbnail' => $collection->thumbnail,
            'shops_count' => $collection->shops_count,
            'contains_shop' => false,
            'last_shop_added_at' => $collection->last_shop_added_at
        ];

        return response()->json(['collection' => $data['collection']]);
    }

    public function listCollections(Request $request)
    {
        /*$collections = Auth::user()
            ->collections()
            ->withCount('shops') // Adds a 'stores_count' attribute
            ->orderBy('created_at', 'desc')
            ->paginate(7);
        */
        $userId = Auth::id();
        $shopId = $request->input('shop_id');
    
        $collections = Collection::where('collections.user_id', $userId)
                ->leftJoin('shop_collections', 'collections.id', '=', 'shop_collections.collection_id')
                ->withCount('shops')
                ->select('collections.*', DB::raw('MAX(shop_collections.updated_at) as last_shop_added_at'))
                ->groupBy('collections.id', 'collections.user_id', 'collections.name', 'collections.thumbnail', 'collections.created_at', 'collections.updated_at')
                ->orderBy('last_shop_added_at', 'desc')
                ->paginate(7);
                
        $data['collections'] = [];

        foreach ($collections as $index => $collection) {
            $hasShop = false;
            if ($shopId) {
                $hasShop = $collection->shops()->where('shop_id', $shopId)->exists();
            }
            
            $data['collections'][$index] = [
                'id' => $collection->id,
                'name' => $collection->name,
                'thumbnail' => $collection->thumbnail,
                'shops_count' => $collection->shops_count,
                'contains_shop' => $hasShop,
                'last_shop_added_at' => $collection->last_shop_added_at
            ];
        }

        $nextPage = null;
        if ($collections->nextPageUrl()) {
            $nextPage = $collections->currentPage() + 1;
        }

        return response()->json([
            'next_page' => $nextPage,
            'collections' => $data['collections'],
        ], 200);
    }

    public function saveStoreToCollection(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'collection_id' => 'required|exists:collections,id'
        ]);

        $shop_id = $request->shop_id;
        $collectionId = $request->collection_id;
        $collection = Collection::find($collectionId);

        // Check if collection exists and belongs to the authenticated user
        if (!$collection || $collection->user_id !== Auth::id()) {
            return response()->json(['message' => 'Collection not found or access denied.'], 404);
        }

        // Check if the shop is already in the collection
        if ($collection->shops()->where('shop_id', $shop_id)->exists()) {
            return response()->json(['message' => 'shop is already in the collection.'], 409);
        }

        // Add the shop to the collection
        $collection->shops()->attach($shop_id);

        return response()->json(['message' => 'shop added to collection successfully.']);
    }

    public function getCollectionDetails(Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id'
        ]);

        $collectionId = $request->collection_id;
        $userId = Auth::id();

        // Find the collection with the related shops
        $collection = Collection::where('id', $collectionId)
            ->where('user_id', $userId) // Ensure the collection belongs to the user
            ->with('shops') // Assuming 'stores' is the relationship name
            ->first();

        if (!$collection) {
            return response()->json(['message' => 'Collection not found.'], 404);
        }

        // Format each shop using the getShop function
        $formattedShops = $collection->shops->map(function ($shop) {
            return getShop($shop); // Assuming getShop is a function that formats the shop data
        });

        return response()->json([
            'collection' => [
                'id' => $collection->id,
                'name' => $collection->name,
                'thumbnail' => $collection->thumbnail,
            ],
            'stores' => $formattedShops
        ]);
    }

    public function updateCollection(Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'name' => 'sometimes|string|max:255',
            'thumbnail' => 'nullable|string|max:2048', // Assuming thumbnail is an image file
        ]);

        $collection = Collection::find($request->collection_id);

        // Ensure the collection belongs to the authenticated user
        if ($collection->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized access to the collection.'], 403);
        }

        // Update the name if provided
        if ($request->has('name')) {
            $collection->name = $request->name;
        }

        // Update the thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            // Store the new thumbnail and get its path
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');

            // If the collection already has a thumbnail, delete the old one
            if ($collection->thumbnail) {
                //Storage::disk('public')->delete($collection->thumbnail);
            }

            // Update the collection with the new thumbnail path
            $collection->thumbnail = $thumbnailPath;
        }

        $collection->save();

        return response()->json(['message' => 'Collection updated successfully.', 'collection' => $collection]);
    }

    public function removeStoreFromCollection(Request $request)
    {
        $request->validate([
            'collection_id' => 'required',
            'shop_id' => 'required',
        ]);

        $collectionId = $request->collection_id;
        $shopId = $request->shop_id;
        $userId = Auth::id();

        $collection = Collection::where('id', $collectionId)
                                ->where('user_id', $userId)
                                ->first();

        if (!$collection) {
            return response()->json(['message' => 'Collection not found or access denied.'], 404);
        }

        if (!$collection->shops()->where('shop_id', $shopId)->exists()) {
            return response()->json(['message' => 'Store not found in collection.'], 404);
        }

        // Detach the shop from the collection
        $collection->shops()->detach($shopId);

        // Check if the shop exists in any other collections of the user
        $existsInOtherCollections = Collection::where('user_id', $userId)
                                            ->where('id', '!=', $collectionId)
                                            ->whereHas('shops', function($query) use ($shopId) {
                                                $query->where('shop_id', $shopId);
                                            })
                                            ->exists();

        return response()->json([
            'message' => 'Store removed from collection successfully.',
            'exists_in_other_collections' => $existsInOtherCollections
        ]);
    }


    public function deleteCollection(Request $request)
    {
        $request->validate([
            'collection_id' => 'required',
        ]);
        $collectionId = $request->collection_id;

        $userId = Auth::id();

        // Find the collection, ensuring it belongs to the authenticated user
        $collection = Collection::where('id', $collectionId)
                                ->where('user_id', $userId)
                                ->first();

        if (!$collection) {
            return response()->json(['message' => 'Collection not found or access denied.'], 404);
        }

        // Delete the collection along with its relationships
        $collection->shops()->detach(); // Detach all associated stores
        $collection->delete(); // Delete the collection

        return response()->json(['message' => 'Collection deleted successfully.']);
    }
}
