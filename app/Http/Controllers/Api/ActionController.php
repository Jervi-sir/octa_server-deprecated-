<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\User;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\UserFollowing;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{

    public function getSavedItems(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
            'category_name'   => 'nullable',
        ]);

        $data = $request->all();
        $auth = auth()->user();

        $savesQuery = $auth->savedItems();
       
      
        if (!empty($data['category_name'])) {
            $category_id = ProductType::where('name', 'like', '%' . $data['category_name'] . '%')->first()->id;
            $savesQuery = $savesQuery->where('product_type_id', $category_id);
        }
        
        $saves = $savesQuery->orderBy('id', 'desc')->paginate(7);
        
        $data['saves'] = [];
        foreach ($saves as $key => $save) {
            $data['saves'][$key] = getItem($save);
        }

        $nextPage = null;
        if ($saves->nextPageUrl()) {
            $nextPage = $saves->currentPage() + 1;
        }
        
        return response()->json([
            'next_page' => $nextPage,
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
            'page' => 'nullable',
            'username' => 'nullable'
        ]);
    
        $auth = auth()->user();
    
        // Get merged friends collection
        $friends = $auth->friends();
    
        // Apply username filter if provided
        if (!empty($request->username)) {
            $friends = $friends->filter(function ($friend) use ($request) {
                return str_contains(strtolower($friend->username), strtolower($request->username));
            });
        }
    
        $data['friends'] = [];
    
        foreach ($friends as $friend) {
            $data['friends'][] = getFriendToSendTo($friend);
        }
    
        return response()->json([
            'friends' => $data['friends']
        ]);
    }


    public function likeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|different:auth()->id()',
        ]);

        $data = $request->all();
        $user = auth()->user();
        $user->likedUsers()->syncWithoutDetaching([$data['user_id']]);

        return response()->json(['message' => 'User liked successfully.']);
    }

    public function unlikeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|different:auth()->id()',
        ]);
        $data = $request->all();
        $user = auth()->user();

        $alreadyLiked = $user->likedUsers()->where('liked_id', $data['user_id'])->exists();

        if ($alreadyLiked) {
            // Detach the liked user from the authenticated user's likedUsers
            $user->likedUsers()->detach($data['user_id']);
    
            return response()->json(['message' => 'User unliked successfully.']);
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
        $usersWhoLikedMe = $user->likedByUsers()->paginate(10); // 10 users per page
        $data['users'] = [];
        foreach ($usersWhoLikedMe as $user) {
            $data['users'][] = getProfile($user);
        }

        return response()->json([
            'users' => $data['users']
        ]);
    }

    public function reportItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'reasons' => 'nullable',
        ]);
        
        $data = $request->all();
        $user = auth()->user();
        $item = Item::findOrFail($data['item_id']);

        if ($item->reports()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already reported this item.']);
        }

        $item->increment('nb_reports');

        $item->reports()->create([
            'user_id' => $user->id,
            'reasons' => $request->input('reasons'), // You can get the reason from the request
        ]);

        $item->save();

        return response()->json(['message' => 'Item reported successfully.']);
    }

}
  