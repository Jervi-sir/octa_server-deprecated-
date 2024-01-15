<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id'   => 'required',
        ]);

        $receiverId = $request->receiver_id;

        // Prevent sending a request to oneself
        if ($receiverId == Auth::id()) {
            return response()->json(['message' => 'You cannot send a friend request to yourself.'], 400);
        }

        // Check if the request already exists
        if (FriendRequest::where('sender_id', Auth::id())->where('receiver_id', $receiverId)->exists()) {
            return response()->json(['message' => 'Friend request already sent.'], 400);
        }

        // Create friend request
        $friendRequest = FriendRequest::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
        ]);

        return response()->json($friendRequest, 201);
    }

    public function acceptRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required', // Expect a user_id in the request
        ]);
        
        $userId = $request->user_id;
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
    
        return response()->json(['message' => 'Friend request accepted.'], 200);
    }

    public function showReceivedRequests(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $requests = FriendRequest::where('receiver_id', Auth::id())->paginate(7);
        return response()->json($requests);
    }

    public function showSentRequests(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
        ]);

        $requests = FriendRequest::where('sender_id', Auth::id())->paginate(7);
        return response()->json($requests);
    }

    public function showFriendList(Request $request)
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
}
