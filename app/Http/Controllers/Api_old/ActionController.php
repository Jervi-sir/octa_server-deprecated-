<?php

namespace App\Http\Controllers\Api_old;

use App\Models\Item;
use App\Models\User;
use App\Models\ProductType;
use App\Models\UserFollowing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{

    public function followUser(Request $request)
    {
        $request->validate([
            'user_id'   => 'required',
        ]);

        $data = $request->all();
        $auth = auth()->user();
        $userToFollow = User::find($data['user_id']);

        if ($userToFollow->rls_getFollowers->contains(auth()->user())) {
            return response()->json('Already following this user', 422);  // Unprocessable Entity
        }

        $userToFollow->rls_getFollowers()->attach(auth()->id(), ['created_at' => now(), 'updated_at' => now()]);

        /*
        $following = new UserFollowing();
        $following->follower_id = $auth->id;
        $following->following_id = $userToFollow->id;
        $following->save();
        */
        return response()->json('Following Successfully', 200);
    }

    public function unfollowUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Ensure user exists
        ]);
    
        $userToFollow = User::find($request->user_id);
    
        // Check if user is already following
        if (!$userToFollow->rls_getFollowers()->where('follower_id', auth()->id())->exists()) {
            return response()->json('Not following this user', 422);
        }
    
        $userToFollow->rls_getFollowers()->detach(auth()->id());
    
        return response()->json('Unfollowed successfully', 200);
    }

    public function getMyFollowings(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $data = $request->all();
        $auth = auth()->user();
        
        $followings = $auth->rls_getFollowing()->orderBy('id', 'desc')->paginate(7);
        
        $data['followings'] =  [];
        foreach ($followings as $key => $following) {
            $data['followings'][$key] = OP_getProfile($following);
        }

        return response()->json([
            'pagination' => [
                'total' => $followings->total(),
                'per_page' => $followings->perPage(),
                'current_page' => $followings->currentPage(),
                'last_page' => $followings->lastPage(),
                'from' => $followings->firstItem(),
                'to' => $followings->lastItem(),
            ],
            'users' => $data['followings'],
            
        ]);
    }

    public function getMyFollowers(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $data = $request->all();
        $auth = auth()->user();
        
        $followings = $auth->rls_getFollowers()->orderBy('id', 'desc')->paginate(7);
        
        $data['followings'] =  [];
        foreach ($followings as $key => $following) {
            $data['followings'][$key] = OP_getProfile($following);
        }

        return response()->json([
            'pagination' => [
                'total' => $followings->total(),
                'per_page' => $followings->perPage(),
                'current_page' => $followings->currentPage(),
                'last_page' => $followings->lastPage(),
                'from' => $followings->firstItem(),
                'to' => $followings->lastItem(),
            ],
            'users' => $data['followings'],
            
        ]);
    }

    public function suggestFriendToShareWith(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);
    
        $auth = auth()->user();
    
        // Get merged friends collection
        $friends = $auth->rls_friends();
    
        // Apply username filter if provided
        if (!empty($request->username)) {
            $friends = $friends->filter(function ($friend) use ($request) {
                return str_contains(strtolower($friend->username), strtolower($request->username));
            });
        }
    
        $data['friends'] = [];
    
        foreach ($friends as $friend) {
            $data['friends'][] = OP_getFriendToSendTo($friend);
        }
    
        return response()->json([
            'friends' => $data['friends']
        ]);
    }







}
  