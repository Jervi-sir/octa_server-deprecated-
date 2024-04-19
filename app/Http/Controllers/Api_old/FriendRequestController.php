<?php

namespace App\Http\Controllers\Api_old;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id', // Ensure recipient exists
        ]);

        $receiverId = $request->receiver_id;
        $receiver = User::find($receiverId);
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

        return response()->json('Friend request sent successfully', 201);

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
    
        return response()->json(['status' => 'friends'], 201);
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
            'message' => 'Friend request rejected.',
            'status' => 'request_rejected',
        ], 200);
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
        $friends = $auth->rls_friends();
    
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
