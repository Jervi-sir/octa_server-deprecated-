<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OpMyProfileController extends Controller
{
    public function showMyProfile(Request $request)
    {
        $user = Auth::user();

        $collections = Collection::where('collections.user_id', $user->id)
            ->leftJoin('shop_collections', 'collections.id', '=', 'shop_collections.collection_id')
            ->withCount('rls_shops')
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
            'my_profile' => OP_getMyProfile($user),
            'collections' => $data['collections'],
        ], 200);
    }

    public function updateMyProfile(Request $request)
    {
        $request->validate([
            'phone_number'   => 'nullable|string',
            'email'     => 'nullable|string',
            'name'      => 'nullable|string',
            'username'     => 'nullable|string',
            'bio'     => 'nullable|string',
            'location'     => 'nullable|numeric',
            'profile_image'   => 'nullable|string',
            'contacts'    => 'nullable|string'
        ]);
        
        $user = auth()->user();

        if ($request->has('profile_image')) {
            $imagePath = 'public/images/' . uniqid() . '.png';
            Storage::put($imagePath, base64_decode($request->shop_image));
            $user->profile_images = env('API_URL') . Storage::url($imagePath);
            //$shop->shop_image = $request->shop_image;
        }

        $user->fill($request->only([
            'phone_number',
            'email',
            'name', 
            'username', 
            'bio', 
            'location', 
            'contacts' 
        ]));
    
        $user->save();

        return response()->json([
            'status'    => true,
            'message'   => 'updated successfully',
            'my_profile'      => OP_getMyProfile($user),
        ], 200);
    }

    public function addSocial(Request $request)
    {
        $request->validate([
            'platform' => 'required',
            'profileURL' => 'required',
        ]);

        $user = auth()->user();
        $contacts = json_decode($user->contacts, true) ?? [];

        // Determine the next ID
        $nextId = 0;
        foreach ($contacts as $contact) {
            if ($contact['id'] > $nextId) {
                $nextId = $contact['id'];
            }
        }
        $nextId++;  // Increment to get the next ID

        $contacts[] = [
            'id' => $nextId,  // Use the next ID
            'platform' => $request->platform,
            'profileURL' => $request->profileURL,
        ];

        $user->contacts = json_encode($contacts);
        $user->save();

        return response()->json([
            'message' => 'Contact added successfully!',
            'my_profile' => OP_getMyProfile($user)  // Ensure this method returns the updated shop info
        ]);
    }
}
