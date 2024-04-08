<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

    protected $table = 'friend_requests';

    // Fillable fields to protect against mass-assignment
    protected $fillable = [
        'sender_id',
        'receiver_id'
    ];

    /**
     * Get the user that sent the friend request.
     */
    public function rls_sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user that received the friend request.
     */
    public function rls_receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
}
