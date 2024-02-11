<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function showMyProfile(Request $request)
    {
        $user = Auth::user();
        $data['user'] = array_merge(getProfile($user), [
            'bio' => $user->bio,
            'contacts' => $user->contacts,
            'nb_likes' => 0, // Adjust this according to your actual logic
            'nb_friends' => $user->friends()->count(),
            'isPremium' => $user->isPremium,
        ]);

        $collections = Collection::where('collections.user_id', $user->id)
            ->leftJoin('shop_collections', 'collections.id', '=', 'shop_collections.collection_id')
            ->withCount('shops')
            ->select('collections.*', DB::raw('MAX(shop_collections.updated_at) as last_shop_added_at'))
            ->groupBy('collections.id', 'collections.user_id', 'collections.name', 'collections.thumbnail', 'collections.created_at', 'collections.updated_at')
            ->orderBy('last_shop_added_at', 'desc')
            ->paginate(20);

        $data['collections'] = [];
        foreach ($collections as $index => $collection) {
            
            $data['collections'][$index] = [
                'id' => $collection->id,
                'name' => $collection->name,
                'thumbnail' => $collection->thumbnail,
                'shops_count' => $collection->shops_count,
                'last_shop_added_at' => $collection->last_shop_added_at
            ];
        }
        $nextPage = null;
        if ($collections->nextPageUrl()) {
            $nextPage = $collections->currentPage() + 1;
        }

        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => $nextPage,
            'user' => $data['user'],
            'collections' => $data['collections'],
        ], 200);
    }


    public function getSavedItems()
    {
        $user = Auth::user();
        $items = $user->savedItems;
        $data['items'] = [];
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'items' => $data['items'],
        ], 200);
    }

    public function updateMyProfile(Request $request)
    {

        $request->validate([
            'phone_number'   => 'nullable|string',
            'email'     => 'nullable|string',
            'name'      => 'nullable|string',
            'username'     => 'nullable|string',
            'bio'     => 'nullable|numeric',
            'location'     => 'nullable|numeric',
            'profile_images'   => 'nullable|string',
            'contacts'    => 'nullable|string'
        ]);
        
        $data = $request->all();
        $user = auth()->user();

        $fieldsToUpdate = ['phone_number', 'email', 'name', 'username', 'bio', 'location', 'profile_images', 'contacts'];
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $user->{$field} = $request->{$field};
            }
        }
        $user->save();

        return response()->json([
            'status'    => true,
            'message'   => 'updated successfully',
            'user'      => $user,
        ], 200);
    }

    public function showMyMap(Request $request)
    {
        $request->validate([
            'page'   => 'nullable|numeric',
        ]);
        
        $data = $request->all();
        $user = auth()->user();
        $map = $user->game_map;

        return response()->json([
            'status'    => true,
            'message'   => 'updated successfully',
            'map'      => $map,
        ], 200);  
    }
}
