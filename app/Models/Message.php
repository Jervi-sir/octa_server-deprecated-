<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['conversation_id', 'sender_id', 'message_text', 'read_status', 'item_id'];


    // Message belongs to a receiver
    public function rls_receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // If using items
    public function rls_item()
    {
        return $this->belongsTo(Item::class);
    }

    
    public function rls_conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function rls_sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    
}
