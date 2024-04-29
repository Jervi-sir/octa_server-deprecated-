<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpConversationController extends Controller
{
    public function suggestFriendToShareWith(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
            'username' => 'nullable'
        ]);
    
        $auth = auth()->user();
    
        // Get IDs of users who are blocked or have blocked the authenticated user
        $blockedUsers = $auth->rls_usersIBlocked()->pluck('users.id')->toArray();
        $usersWhoBlockedMe = $auth->rls_usersBlockingMe()->pluck('users.id')->toArray();
        $blockedUserIds = array_unique(array_merge($blockedUsers, $usersWhoBlockedMe));
    
        // Get merged friends collection
        $friendsQuery = $auth->rls_friends()->whereNotIn('users.id', $blockedUserIds);
    
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

        // Update conversation's updated_at and increment nb_unread for recipient
        $conversation->touch(); // This will update the updated_at timestamp
        if ($user->id != $conversation->user1_id) {
            $conversation->increment('nb_unread'); // Assumes user1_id is always the recipient
        } else {
            // If user2_id is the recipient, increment nb_unread
            // Assuming here we have some method to update nb_unread for user2_id
            $conversation->increment('nb_unread');
        }

        $conversation->last_message = Item::find($validatedData['item_id'])->name;
        $conversation->save();

        return response()->json($message, 201);
    }

    public function listConversations(Request $request)
    {
        $auth = Auth::user();
        $perPage = 10; // Define how many items per page

        // Fetch conversations with the latest message for each
        $allConversations = $auth->rls_conversations()->orderByDesc('updated_at')->paginate($perPage);
       
        $data['conversations'] = $allConversations->map(function ($conversation) {
            $chattingWith = $conversation->getOtherUserAttribute();
            return [
                'id' => $conversation->id,
                'friend_profile_pic' => $chattingWith->profile_images,
                'friend_username' => $chattingWith->username,
                'last_message' => $conversation->last_message,
            ];
        });
        
        $nextPage = null;
        if ($allConversations->nextPageUrl()) {
            $nextPage = $allConversations->currentPage() + 1;
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => $nextPage,
            'chats' => $data['conversations'],
        ], 200);
    }

    public function showThisConversation(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'page' => 'nullable'
        ]);

        $auth = Auth::user();
        $perPage = 10;
        $conversation = Conversation::find($request->conversation_id);
        $conversation->nb_unread = 0;
        $conversation->save();

        $interlocutor_id = $conversation->user1_id === $auth->id ? $conversation->user2_id : $conversation->user1_id;
        $interlocutor = User::find($interlocutor_id);
        
        $messages = $conversation->rls_messages()->orderByDesc('updated_at')->paginate($perPage);
        $data['messages'] = $messages->map(function ($message) use($auth) {
            $item = Item::find($message->item_id);
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message_text' => $message->message_text,
                'created_at' => $message->created_at,
                'sent_by_me' => $auth->id === $message->sender_id ? true : false,
                'item' => OP_getItem($item),
                'read_status' => $message->read_status,
            ];
        });
        $nextPage = null;
        if ($messages->nextPageUrl()) {
            $nextPage = $messages->currentPage() + 1;
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_page' => $nextPage,
            //'block_exists' => $auth->rls_blockExists($interlocutor),
            'conversation' => [
                'id' => $conversation->id,
                'created_at' => $conversation->created_at,
                'updated_at' => $conversation->updated_at,
            ],
            'friend' => [
                'id' => $interlocutor->id,
                'profile_images' => $interlocutor->profile_images,
                'username' => $interlocutor->username,
                'isBlocked' => $auth->rls_haveIBlockedHim($interlocutor),
            ],
            'my_self' => [
                'profile_images' => $auth->profile_images,
                'username' => $auth->username,
                'iamBlocked' => $auth->rls_hasHeBlockedMe($interlocutor),
            ],
            'messages' => $data['messages']
        ], 200);
    }



    public function showMessage($messageId)
    {
        $user = Auth::user();
        $message = Message::where('id', $messageId)
            ->whereHas('conversation', function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('rls__id', $user->id);
            })->firstOrFail();

        return response()->json($message);
    }

    public function unSendMessage(Request $request)
    {
        $validated = $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);

        $user = Auth::user();
        $messageId = $validated['message_id'];

        $message = Message::where('id', $messageId)
            ->where('sender_id', $user->id) // Ensure the requester is the sender
            ->firstOrFail();

        // For soft delete
        $message->delete();

        return response()->json(['message' => 'Message unsent successfully.']);
    }


    public function deleteConversation(Request $request, )
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        $user = Auth::user();

        $conversation = Conversation::where('id', $request->conversation_id)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('rls__id', $user->id);
            })->first();

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found or access denied.'], 404);
        }

        $conversation->delete();
        return response()->json(['message' => 'Conversation deleted successfully.']);
    }

    public function ShowThisItem(Request $request, $item_id)
    {
        $item = Item::find($item_id);
        $data['item'] = OP_getItem($item);

        return response()->json([
            'message' => 'returning an Item',
            'item' => $data['item']
        ]);

    }

    public function deleteThisConversation(Request $request) 
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        $auth = auth()->user();
        $conversation = $auth->rls_conversations()->where('id', $request->conversation_id)->first();

        $conversation->delete();

        return response()->json([
            'message' => 'Conversation deleted Successfully',
        ]);
    }
}
