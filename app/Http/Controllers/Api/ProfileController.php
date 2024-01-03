<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function showMyProfile(Request $request)
    {
        $user = Auth::user();
        $data['user'] = [
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'name' => $user->name,
            'username' => $user->username,
            'bio' => $user->bio,
            'location' => $user->location,
            'profile_images' => $user->profile_images,
            'contacts' => $user->contacts,
            'nb_likes' => $user->nb_likes,
            'nb_followers' => $user->nb_followers,
            'isPremium' => $user->isPremium,
            'credit' => $user->credit,
            'game_map' => $user->game_map,
            //'profile_images' => imageUrl('users', $user->profile_images),
        ];

        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'user' => $data['user'],
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
            'phone_number'   => 'required|string',
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

        $user->phone_number = $data['phone_number'];
        $fieldsToUpdate = ['email', 'name', 'username', 'bio', 'location', 'profile_images', 'contacts'];
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
