<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;
    protected $table = 'friends';

    // Fillable fields to protect against mass-assignment
    protected $fillable = [
        'user_id',
        'friend_id'
    ];

    /**
     * Get the user associated with the friendship.
     */
    public function rls_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the friend user associated with the friendship.
     */
    public function rls_friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

}
