<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user1_id', 'user2_id']; // Add this line

    public function rls_users()
    {
        return $this->belongsToMany(User::class);
    }

    public function rls_user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function rls_user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }
    
    public function rls_messages()
    {
        return $this->hasMany(Message::class);
    }

    // Accessor to get the other user
    public function getOtherUserAttribute()
    {
        $authUserId = auth()->id(); // Get the authenticated user's ID

        // Return the other user based on who is not the authenticated user
        if ($this->user1_id == $authUserId) {
            return $this->rls_user2; // Return user2 if the authenticated user is user1
        } else {
            return $this->rls_user1; // Return user1 if the authenticated user is user2
        }
    }
}
