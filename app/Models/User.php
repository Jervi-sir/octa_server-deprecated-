<?php

namespace App\Models;

use App\Models\Item;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        '*'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_images' => 'array',
    ];

    public function savedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'user_saves', 'user_id', 'item_id');
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function sentRequests()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedRequests()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function friends()
    {
        $friendsAsUser = $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
        $friendsAsFriend = $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id');
        return $friendsAsUser->get()->merge($friendsAsFriend->get());
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }
    

    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
                           ->orWhere('user2_id', $this->id);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'user_likes', 'liker_id', 'liked_id');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_likes', 'liked_id', 'liker_id');
    }

}
