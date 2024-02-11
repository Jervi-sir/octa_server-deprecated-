<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function listConversations(Request $request)
    {
        $user = Auth::user();
        $userId = Auth::id();
        $perPage = 10; // Define how many items per page
    
        // Fetch conversations with the latest message for each
        $allConversations = $user->conversations()
                        ->with(['user1', 'user2', 'messages' => function ($query) {
                            $query->latest()->first();
                        }])
                        ->get()
                        ->map(function ($conversation) use ($userId) {
                    // Determine the friend's details (assuming the friend is not the current user)
                    $friend = ($conversation->user1_id == $userId) ? $conversation->user2 : $conversation->user1;

                    $lastMessage = $conversation->messages->first();

                    // Count unread messages in this conversation
                    $unreadCount = $conversation->messages->where('read_status', false)->count();

                    return [
                        'id' => $conversation->id,
                        'friend_id' => $friend->id,
                        'friend_profile_pic' => $friend->profile_images ? ($friend->profile_images)[0] : null,
                        'friend_username' => $friend->username,
                        'last_message' => $lastMessage ? $lastMessage->item->name : null,
                        'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                        'last_message_read' => $lastMessage ? (bool) $lastMessage->read_status : null,
                        'unread_messages_count' => $unreadCount,
                    ];
                })
                ->sortByDesc('last_message_time');

        $totalPages = ceil($allConversations->count() / $perPage);
        $currentPage = $request->page ?? 1;
        $conversations = $allConversations->forPage($currentPage, $perPage)->values();
    
        $nextPage = $currentPage < $totalPages ? $currentPage + 1 : null;

        
        return response()->json([
            'next_page' => $nextPage,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'current_page' => $currentPage,
            'chats' => $conversations,
        ]);
    }

    public function showThisConversation(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'page' => 'nullable'
        ]);
        
        $user = Auth::user();
        $perPage = 10; // Define how many messages you want per page
    
        $conversation = Conversation::where('id', $request->conversation_id)
            ->where(function ($query) use ($user) {
                $query->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->firstOrFail();

        // Identify the friend in the conversation
        $friendId = ($conversation->user1_id == $user->id) ? $conversation->user2_id : $conversation->user1_id;
        $friend = User::select('id', 'username', 'profile_images')->find($friendId);
        $data['friend'] = [
            'id' => $friend->id,
            'username' => $friend->username,
            'profile_images' =>  $friend->profile_images ? $friend->profile_images[0] : null,
        ];
        $data['my_self'] = [
            'id' => $user->id,
            'username' => $user->username,
            'profile_images' =>  $user->profile_images ? $user->profile_images[0] : null,
        ];

        // Paginate the messages
        $messages = $conversation->messages()
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage, ['*'], 'page', $request->page ?? 1);

        // Transform each message to include 'sent_by_me' attribute
        $messages->getCollection()->transform(function ($message) use ($user) {
            $message->sent_by_me = $message->sender_id === $user->id;
            return $message;
        });

        $nextPage = $messages->currentPage() < $messages->lastPage() 
                    ? $messages->currentPage() + 1 
                    : null;
        
        $data['messages'] = [];
        foreach ($messages->items() as $index => $message) {
            $data['messages'][$index] = [
                "id" => $message->id,
                "conversation_id" => $message->conversation_id,
                "sender_id" => $message->sender_id,
                "message_text" => $message->message_text,
                "item_id" => $message->item_id,
                "read_status"=> $message->read_status,
                "created_at" => $message->created_at,
                "sent_by_me" => $message->sent_by_me,
                "item" => getItem(Item::find($message->item_id))
            ];
        }

        return response()->json([
            'conversation' => $conversation,
            'friend' => $data['friend'], // Include friend's details
            'my_self' => $data['my_self'],
            'messages' => $data['messages'], // Get the transformed messages
            'next_page' => $nextPage,
            'current_page' => $messages->currentPage(),
            'last_page' => $messages->lastPage(),
        ]);
                
    }
    

    public function storeMessage(Request $request)
    {
        $validatedData = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message_text' => 'nullable|string',
            'item_id' => 'sometimes|required_without:message_text|exists:items,id'
        ]);
    
        $user = Auth::user();
        $recipientId = $validatedData['recipient_id'];
        $friends = $user->friends();

        // Check if they are friends
        if (!$friends->contains('id', $recipientId)) {
            return response()->json(['error' => 'You can only message your friends.'], 403);
        }
    
        // Find an existing conversation or create a new one
        $conversation = Conversation::firstOrCreate(
            [
                'user1_id' => $user->id, 
                'user2_id' => $recipientId
            ],
            [
                'user1_id' => $user->id, 
                'user2_id' => $recipientId
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

    public function showMessage($messageId)
    {
        $user = Auth::user();
        $message = Message::where('id', $messageId)
                          ->whereHas('conversation', function ($query) use ($user) {
                              $query->where('user1_id', $user->id)
                                    ->orWhere('user2_id', $user->id);
                                })->firstOrFail();
                                
        return response()->json($message);
    }                       

    public function unsendMessage(Request $request)
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
                            ->where(function($query) use ($user) {
                                $query->where('user1_id', $user->id)
                                    ->orWhere('user2_id', $user->id);
                            })->first();

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found or access denied.'], 404);
        }

        $conversation->delete();
        return response()->json(['message' => 'Conversation deleted successfully.']);
    }

}
