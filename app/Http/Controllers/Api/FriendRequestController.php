<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
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

    /**
     * Accept a friend request.
     */
    public function acceptRequest($requestId)
    {
        $friendRequest = FriendRequest::find($requestId);

        // Check if request exists and is meant for the authenticated user
        if (!$friendRequest || $friendRequest->receiver_id !== Auth::id()) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }

        // Create a friendship
        Friend::create([
            'user_id' => Auth::id(),
            'friend_id' => $friendRequest->sender_id,
        ]);

        // Delete the friend request
        $friendRequest->delete();

        return response()->json(['message' => 'Friend request accepted.']);
    }

    /**
     * Show all received friend requests.
     */
    public function showReceivedRequests()
    {
        $requests = FriendRequest::where('receiver_id', Auth::id())->get();
        return response()->json($requests);
    }

    /**
     * Show all sent friend requests.
     */
    public function showSentRequests()
    {
        $requests = FriendRequest::where('sender_id', Auth::id())->get();
        return response()->json($requests);
    }

}
