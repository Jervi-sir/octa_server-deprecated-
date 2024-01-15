<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        '*'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chats', 'user_id', 'friend_id')
                    ->withTimestamps();
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'chat_items')
                    ->withTimestamps();
    }
}
