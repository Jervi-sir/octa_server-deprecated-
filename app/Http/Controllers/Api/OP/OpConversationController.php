<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class OpConversationController extends Controller
{
    public function suggestFriendToShareWith(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);
    
        $auth = auth()->user();
    
        // Get merged friends collection
        $friendsQuery = $auth->rls_friends();
    
        // Apply username filter if provided
        // Apply username filter if provided
        if ($request->filled('username')) {
            $friendsQuery = $friendsQuery->where('username', 'like', '%' . $request->username . '%');
        }
        $friends = $friendsQuery->paginate(10, ['*'], 'page', $request->page);

        $data['friends'] = $friends->map(function ($friend) {
            return OP_getFriendToSendTo($friend);
        });
    
        return response()->json([
            'friends' => $data['friends']
        ]);
    }

    
    public function sendMessageTo(Request $request)
    {
        $validatedData = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message_text' => 'nullable|string',
            'item_id' => 'sometimes|required_without:message_text|exists:items,id'
        ]);
    
        $user = auth()->user();
        $recipientId = $validatedData['recipient_id'];

        if (!$user->rls_friends->pluck('id')->contains($recipientId)) {
            return response()->json(['error' => 'You can only message your friends.'], 403);
        }
    
        $ids = [$user->id, $recipientId];
        sort($ids);
        // Find an existing conversation or create a new one
        $conversation = Conversation::firstOrCreate(
            [
                'user1_id' => $ids[0],
                'user2_id' => $ids[1]
            ]
        );
    
        // Create and save the message
        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = $user->id;
        $message->message_text = isset($validatedData['message_text']) ? $validatedData['message_text'] : null;
        $message->item_id = $validatedData['item_id'];
        $message->save();
    
        return response()->json($message, 201);
    }
}
