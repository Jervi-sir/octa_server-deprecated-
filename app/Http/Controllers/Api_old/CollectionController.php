<?php

namespace App\Http\Controllers\Api_old;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{





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
        
        // Format each shop using the OP_getShop function
        $formattedShops = $collection->rls_shops->map(function ($shop) use ($collection) {
            $nbNew = $shop->rls_collections->find($collection->id)->pivot->nb_new ?? 0;
            $formattedShop = OP_getShop($shop);
            return array_merge($formattedShop, ['nb_new' => $nbNew]);
        });

        return response()->json([
            'collection' => [
                'id' => $collection->id,
                'name' => $collection->name,
                'thumbnail' => $collection->thumbnail,
                'stores_count' => $collection->rls_shops->count(),
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
        $collection->rls_shops()->detach(); // Detach all associated stores
        $collection->delete(); // Delete the collection

        return response()->json(['message' => 'Collection deleted successfully.']);
    }
}
