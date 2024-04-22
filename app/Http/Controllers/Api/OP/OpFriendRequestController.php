<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpFriendRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id', // Ensure recipient exists
        ]);

        $receiverId = $request->receiver_id;
        $sender = auth()->user();

        // Check for existing requests or a reciprocal friendship
        $existingRequest = FriendRequest::where(function($query) use ($sender, $receiverId) {
            $query->where('sender_id', $sender->id)->where('receiver_id', $receiverId);
            })->orWhere(function($query) use ($sender, $receiverId) {
                $query->where('sender_id', $receiverId)->where('receiver_id', $sender->id);
            })->exists();
            
        if ($existingRequest || $sender->rls_isFriendWith(User::find($receiverId))) {
            // Handle existing requests/friendship (return appropriate response)
            return response()->json('Already friends or a Request exists already', 422);
        }
        

        $sender->rls_sentFriendRequests()->create([
            'receiver_id' => $receiverId,
            'created_at' => now(),
            'updated_at' => now(), // If you want to track updates
        ]);

        return response()->json([
            'message' => 'Friend request sent successfully',
            'user' => User::find($receiverId)
        ], 201);
    }

    public function acceptRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required', // Expect a user_id in the request
        ]);
        
        $userId = $request->receiver_id;
        $authId = Auth::id();
    
        $friendRequest = FriendRequest::where('sender_id', $userId)
                                        ->where('receiver_id', $authId)
                                        ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }
    
        // Create a friendship
        Friend::create([
            'user_id' => $authId,
            'friend_id' => $userId,
        ]);
    
        // Delete the friend request
        $friendRequest->delete();
    
        return response()->json([
            'message' => 'friends',
            'user' => User::find($userId)
        ], 201);
    }

    public function rejectRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required', // Expect a user_id in the request
        ]);

        $userId = $request->receiver_id;
        $authId = Auth::id();
        
        $friendRequest = FriendRequest::where('sender_id', $userId)
                                        ->where('receiver_id', $authId)
                                        ->first();
        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }

        // Delete the friend request to reject it
        $friendRequest->delete();
      
        return response()->json([
            'message' => 'Friend request rejected',
            'user' => User::find($userId)
        ], 201);
    }
    public function showReceivedRequests(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);
        $auth = Auth::user();

        $requests = FriendRequest::where('receiver_id', Auth::id())->paginate(7);
        
        $nextPage = null;
        if ($requests->nextPageUrl()) {
            $nextPage = $requests->currentPage() + 1;
        }
        
        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $requests->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $requests->lastPage(),
            'items' => $requests,
        ], 200);
    }

    public function showSentRequests(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $auth = Auth::user();

        $requests = FriendRequest::where('sender_id', Auth::id())->paginate(7);

        $nextPage = null;
        if ($requests->nextPageUrl()) {
            $nextPage = $requests->currentPage() + 1;
        }
        
        return response()->json([
            'my_user_info' => $auth,
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $requests->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $requests->lastPage(),
            'items' => $requests,
        ], 200);
    }

    public function showFriendList(Request $request)
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
}
