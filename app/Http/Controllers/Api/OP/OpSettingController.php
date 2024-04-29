<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OpSettingController extends Controller
{
    public function listFriends(Request $request) 
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);
    
        $auth = auth()->user();
    
        // Get merged friends collection
        $friends = $auth->rls_allFriends($perPage = 10, $request->page);
        
        // Apply username filter if provided
        if (!empty($request->username)) {
            $friends = $friends->filter(function ($friend) use ($request) {
                return str_contains(strtolower($friend->username), strtolower($request->username));
            });
        }
    
        $data['friends'] = [];
    
        foreach ($friends as $friend) {
            $data['friends'][] = OP_getProfile($friend);    //old was => OP_getFriendToSendTo($friend)
        }
    
        $nextPage = null;
        if ($friends->nextPageUrl()) {
            $nextPage = $friends->currentPage() + 1;
        }
        
        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $friends->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $friends->lastPage(),
            'friends' => $data['friends']
        ], 200);
    }

    public function listUserILike(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);

        $auth = auth()->user();

        // Paginate users that the current user has liked
        $usersILike = $auth->rls_usersILike()->paginate($perPage = 10, ['*'], 'page', $request->page);
        
        $data['users'] = [];
    
        foreach ($usersILike as $user) {
            $data['users'][] = OP_getProfile($user);
        }

        $nextPage = null;
        if ($usersILike->nextPageUrl()) {
            $nextPage = $usersILike->currentPage() + 1;
        }

        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $usersILike->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $usersILike->lastPage(),
            'users' => $data['users']
        ], 200);
    }

    public function listUserWhoLikedMe(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);

        $auth = auth()->user();

        // Paginate users that the current user has liked
        $usersWhoLikeMe = $auth->rls_usersWhoLikeMe()->paginate($perPage  = 10, ['*'], 'page', $request->page);

        $data['users'] = [];
    
        foreach ($usersWhoLikeMe as $user) {
            $data['users'][] = OP_getProfile($user);
        }

        $nextPage = null;
        if ($usersWhoLikeMe->nextPageUrl()) {
            $nextPage = $usersWhoLikeMe->currentPage() + 1;
        }

        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $usersWhoLikeMe->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $usersWhoLikeMe->lastPage(),
            'users' => $data['users']
        ], 200);
    }

    public function listWhoIBlocked(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);

        $auth = auth()->user();

        // Paginate users that the current user has liked
        $usersIBlocked = $auth->rls_usersIBlocked()->paginate($perPage = 10, ['*'], 'page', $request->page);

        $data['users'] = [];
        foreach ($usersIBlocked as $user) {
            $data['users'][] = OP_getProfile($user); // Assuming OP_getProfile() is a function to format user data
        }
    
        $nextPage = null;
        if ($usersIBlocked->nextPageUrl()) {
            $nextPage = $usersIBlocked->currentPage() + 1;
        }

        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $usersIBlocked->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $usersIBlocked->lastPage(),
            'users' => $data['users']
        ], 200);
    }

    public function listWhoBlockedMe(Request $request)
    {
        $auth = auth()->user();
        $request->validate([
            'page' => 'nullable|integer'
        ]);

        $usersBlockingMe = $auth->rls_usersBlockingMe()->paginate($perPage = 10, ['*'], 'page', $request->page);

        $data['users'] = [];
        foreach ($usersBlockingMe as $user) {
            $data['users'][] = OP_getProfile($user); // Assuming OP_getProfile() is a function to format user data
        }

        $nextPage = null;
        if ($usersBlockingMe->nextPageUrl()) {
            $nextPage = $usersBlockingMe->currentPage() + 1;
        }

        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $usersBlockingMe->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $usersBlockingMe->lastPage(),
            'users' => $data['users']
        ], 200);
    }

}
