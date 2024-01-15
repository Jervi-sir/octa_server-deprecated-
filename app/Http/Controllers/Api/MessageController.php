<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendItem(Request $request, $itemId, $receiverId)
    {
        $senderId = auth()->id();
        $isFriend = Friend::where(function($query) use ($senderId, $receiverId) {
            $query->where('user_id', $senderId)->where('friend_id', $receiverId);
        })->orWhere(function($query) use ($senderId, $receiverId) {
            $query->where('user_id', $receiverId)->where('friend_id', $senderId);
        })->exists();
    
        if (!$isFriend) {
            return response()->json(['message' => 'You can only send items to your friends.'], 403);
        }
    
        // Check if the item exists
        $item = Item::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found.'], 404);
        }
    
        // Attach the item to the recipient
        $item->recipients()->attach($receiverId, ['user_id' => $senderId]);
    
        return response()->json(['message' => 'Item sent successfully.']);
        }

    public function viewItemsWithUser($userId)
    {
        $currentUserId = auth()->id();
        //Check if the users are friends first
        $isFriend = Friend::where(function($query) use ($currentUserId, $userId) {
            $query->where('user_id', $currentUserId)->where('friend_id', $userId);
            })->orWhere(function($query) use ($currentUserId, $userId) {
            $query->where('user_id', $userId)->where('friend_id', $currentUserId);
            })->exists();

        if (!$isFriend) {
            return response()->json(['message' => 'Users are not friends.'], 403);
        }
            
        // Get all items exchanged between the two users
        $items = Item::whereHas('recipients', function($query) use ($currentUserId, $userId) {
            $query->where('user_item.user_id', $currentUserId)->where('user_item.receiver_id', $userId);
        })->orWhereHas('recipients', function($query) use ($currentUserId, $userId) {
            $query->where('user_item.user_id', $userId)->where('user_item.receiver_id', $currentUserId);
        })->with('user') // Load the user who sent the item
        ->orderBy('created_at', 'asc') // Order by creation time
        ->get();
        return response()->json($items);
    }

    
}
