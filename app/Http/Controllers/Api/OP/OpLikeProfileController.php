<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpLikeProfileController extends Controller
{
    public function likeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|different:auth()->id()',
        ]);

        $data = $request->all();
        $user = auth()->user();
        $user->rls_likedUsers()->syncWithoutDetaching([$data['user_id']]);

        return response()->json([
            'message' => 'User liked successfully.',
            'user' => OP_getProfile(User::find($request->user_id))
        ]);
    }

    public function unlikeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|different:auth()->id()',
        ]);
        $data = $request->all();
        $user = auth()->user();

        $alreadyLiked = $user->rls_likedUsers()->where('liked_id', $data['user_id'])->exists();

        if ($alreadyLiked) {
            // Detach the liked user from the authenticated user's rls_likedUsers
            $user->rls_likedUsers()->detach($data['user_id']);
    
            return response()->json([
                'message' => 'User unliked successfully.',
                'user' => OP_getProfile(User::find($request->user_id))
            ]);
        } else {
            return response()->json(['message' => 'You have not liked this user.'], 404);
        }
    }

    public function listLikedByUsers(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);

        $user = Auth::user(); // Get the authenticated user
        
        // Fetch the users who liked the authenticated user with pagination
        $usersWhoLikedMe = $user->rls_usersWhoLikeMe()->paginate(10); // 10 users per page
        
        $data['users'] = [];
        foreach ($usersWhoLikedMe as $user) {
            $data['users'][] = OP_getProfile($user);
        }

        return response()->json([
            'users' => $data['users']
        ]);
    }

    public function listUsersILiked(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);

        $user = Auth::user(); // Get the authenticated user

        // Fetch the users that the authenticated user liked with pagination
        $usersILiked = $user->rls_usersILike()->paginate(10); // 10 users per page
        $data['users'] = [];
        foreach ($usersILiked as $likedUser) {
            $data['users'][] = OP_getProfile($likedUser);
        }

        return response()->json([
            'users' => $data['users']
        ]);
    }
}
