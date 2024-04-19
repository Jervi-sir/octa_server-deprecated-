<?php

namespace App\Http\Controllers\Api_old;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function startChat(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'friend_id' => 'required|exists:users,id|different:user_id',
        ]);

        // Check if a chat already exists between these two users
        $chat = Chat::where(function($query) use ($validatedData) {
            $query->where('user_id', $validatedData['user_id'])
                  ->where('friend_id', $validatedData['friend_id']);
        })->orWhere(function($query) use ($validatedData) {
            $query->where('user_id', $validatedData['friend_id'])
                  ->where('friend_id', $validatedData['user_id']);
        })->first();

        // If chat doesn't exist, create a new one
        if (!$chat) {
            $chat = Chat::create([
                'user_id' => $validatedData['user_id'],
                'friend_id' => $validatedData['friend_id'],
            ]);
        }

        return response()->json(['chat' => $chat], 200);
    }

    /**
     * List all chats for a specific user.
     *
     * @param int $userId
     * @return Response
     */
    public function listChats($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $chats = $user->chats;

        return response()->json(['chats' => $chats], 200);
    }

    /**
     * Get details of a specific chat.
     *
     * @param int $chatId
     * @return Response
     */
    public function getChatDetails($chatId)
    {
        $chat = Chat::with('items')->find($chatId);

        if (!$chat) {
            return response()->json(['message' => 'Chat not found'], 404);
        }

        return response()->json(['chat' => $chat], 200);
    }

    
    public function shareItem(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
        ]);

        // Assuming the sender is the currently authenticated user
        $senderId = Auth::id();

        // Find or create a chat between the sender and recipient
        $chat = Chat::firstOrCreate(
            [
                'user_id' => $senderId, 
                'friend_id' => $validated['recipient_id']
            ],
            [
                'user_id' => $senderId, 
                'friend_id' => $validated['recipient_id']
            ]
        );
        return response()->json(['message' => 'Item shared successfully', 'chatItem' => $chat], 200);

        // Create a new chat item
        $chatItem = ChatItem::create([
            'chat_id' => $chat->id,
            'item_id' => $validated['item_id'],
        ]);

        return response()->json(['message' => 'Item shared successfully', 'chatItem' => $chatItem], 200);
    }


}
