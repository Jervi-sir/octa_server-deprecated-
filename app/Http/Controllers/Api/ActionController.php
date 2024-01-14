<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFollowing;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{

    public function getSavedItems(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $data = $request->all();
        $auth = auth()->user();

        
        $saves = $auth->savedItems()->orderBy('id', 'desc')->paginate(7);
        
        foreach ($saves as $key => $save) {
            $data['saves'][$key] =[
                'id' => $save->id,
                'name' => $save->name,
                'username' => $save->username,
                'profile_images' => $save->profile_images,
            ];
        }

        return response()->json([
            'pagination' => [
                'total' => $saves->total(),
                'per_page' => $saves->perPage(),
                'current_page' => $saves->currentPage(),
                'last_page' => $saves->lastPage(),
                'from' => $saves->firstItem(),
                'to' => $saves->lastItem(),
            ],
            'products' => $data['saves'],
            
        ]);
    }

    public function saveItem(Request $request)
    {
        $request->validate([
            'item_id'   => 'required',
        ]);

        $data = $request->all();
        $user = auth()->user();

        if ($user->savedItems()->where('item_id', $data['item_id'])->exists()) {
            return response()->json([
                'message' => 'Item already saved'
            ], 422);
        }

        $user->savedItems()->attach($data['item_id']);
        return response()->json([
            'message' => 'Item saved'
        ], 200);
    }

    public function unSaveItem(Request $request)
    {
        $request->validate([
            'item_id'   => 'required',
        ]);

        $data = $request->all();
        $user = auth()->user();

        $user->savedItems()->detach($data['item_id']);
        return response()->json([
            'message' => 'unSaved item'
        ], 200);
    }
    
    public function followUser(Request $request)
    {
        $request->validate([
            'user_id'   => 'required',
        ]);

        $data = $request->all();
        $auth = auth()->user();
        $user_to_follow = User::find($data['user_id']);

        $following = new UserFollowing();
        $following->follower_id = $auth->id;
        $following->following_id = $user_to_follow->id;
        $following->save();
        
        return response()->json('Following Successfully', 200);
    }

    public function unfollowUser(Request $request)
    {
        $request->validate([
            'user_id'   => 'required',
        ]);

        $data = $request->all();
        $auth = auth()->user();
        $user_to_follow = User::find($data['user_id']);

        $following = UserFollowing::where('follower_id', $auth->id)
                        ->where('following_id', $user_to_follow->id)
                        ->first();
        $following->delete();
        return response()->json('unFollowing Successfully', 200);
        
    }

    public function getMyFollowings(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $data = $request->all();
        $auth = auth()->user();

        
        $followings = $auth->usersfollowing()->orderBy('id', 'desc')->paginate(7);
        
        foreach ($followings as $key => $following) {
            $data['followings'][$key] =[
                'id' => $following->id,
                'name' => $following->name,
                'username' => $following->username,
                'profile_images' => $following->profile_images,
            ];
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
            'products' => $data['followings'],
            
        ]);
    }

    public function getMyFollowers(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $data = $request->all();
        $auth = auth()->user();

        
        $followings = $auth->usersfollowers()->orderBy('id', 'desc')->paginate(7);
        
        foreach ($followings as $key => $following) {
            $data['followings'][$key] =[
                'id' => $following->id,
                'name' => $following->name,
                'username' => $following->username,
                'profile_images' => $following->profile_images,
            ];
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
            'products' => $data['followings'],
            
        ]);
    }

    public function suggestFriendToShareWith(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        
    }

}
