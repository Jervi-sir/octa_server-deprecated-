<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\Item;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function listChats()
    {
        $userId = Auth::id();
    
        // Fetch the last message in each conversation the user is involved in
        $chats = Message::where('sender_id', $userId)
                        ->orWhere('receiver_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->groupBy(function($message) use ($userId) {
                            // Group by the other user involved in the conversation
                            return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
                        })
                        ->map(function($messages) {
                            // Take the first message from each group as the latest message
                            return $messages->sortByDesc('created_at')->first();
                        });
    
        // Formatting the response
        $formattedChats = $chats->map(function($message) {
            return [
                'friend_id' => $message->sender_id === Auth::id() ? $message->receiver_id : $message->sender_id,
                'last_message' => Item::find($message->item_id)->name, // Assuming 'content' contains the message text
                'last_message_time' => $message->created_at,
                // Include other relevant data, e.g., friend's name, profile picture, etc.
            ];
        });
    
        return response()->json(['chats' => $formattedChats]);
    }

    // Get messages for a specific chat
    public function getMessages(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
            'friend_id'   => 'nullable',
        ]);

        $friendId = $request->friend_id;

        $userId = Auth::id();
        // Logic to fetch messages between the user and $friendId
        $messages = Message::where(function ($query) use ($userId, $friendId) {
            $query->where('sender_id', $userId)->where('receiver_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('sender_id', $friendId)->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    // Send a message in a chat
    public function sendMessage(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
            'friend_id' => 'required',
            'item_id' => 'nullable|exists:items,id'
        ]);
        
        $friendId = $request->friend_id;
        $userId = Auth::id();
        
        $authUser = Auth::user();
        $friends = $authUser->friends();

        $isFriend = $friends->contains('id', $friendId);

        if (!$isFriend) {
            return response()->json(['message' => 'The specified user is not a friend.'], 403);
        }
    
        $message = new Message();
        $message->sender_id = $userId;
        $message->receiver_id = $friendId;
        $message->item_id = $request->item_id;
        $message->save();

        return response()->json(['message' => 'Message sent successfully.', 'data' => $message]);
    }

    public function getChatWithFriend(Request $request)
    {
        $request->validate([
            'page'   => 'nullable',
            'friend_id' => 'required',
        ]);
        $perPage = 7;

        $friendId = $request->friend_id;
        $userId = Auth::id();

        // Fetch messages between the user and the specified friend
        $messages = Message::where(function ($query) use ($userId, $friendId) {
                            $query->where('sender_id', $userId)->where('receiver_id', $friendId);
                        })
                        ->orWhere(function ($query) use ($userId, $friendId) {
                            $query->where('sender_id', $friendId)->where('receiver_id', $userId);
                        })
                        ->orderBy('created_at', 'desc') // Order messages chronologically
                        ->paginate($perPage);

        // Optionally, format the messages or include additional information
        $formattedMessages = $messages->map(function ($message) use ($userId) {
            return [
                'id' => $message->id,
                'content' => $message->content, // Assuming 'content' contains the message text
                'sender_id' => $message->sender_id,
                'sent_at' => $message->created_at,
                'is_sent' => $message->sender_id === $userId, // true if sent by the authenticated user
                'item' => getItem(Item::find($message->item_id)),
                // Include other relevant details like item details if 'item_id' is present
            ];
        });

        return response()->json(['messages' => $formattedMessages]);
    }
}
