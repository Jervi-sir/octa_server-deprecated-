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
            'username' => $user->username,
            'name' => $user->name,
            'isPremium' => $user->isPremium,
            'bio' => $user->bio,
            'contacts' => $user->contacts,
            'nb_followers' => $user->nb_followers,
            'nb_likes' => $user->nb_likes,
            'profile_images' => $user->profile_images,
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
}
