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

    protected $fillable = [
        'phone_number',
        'email',
        'password',
        'username',
        'password_plainText',
        'wilaya_id',
        'wilaya_created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_images' => 'array',
    ];

    
    public function rls_sentRequests() {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }
    
    public function rls_receivedRequests() {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function rls_friends() {
        // First part of the union: friends where the user is the user_id
        $friendsAsUser = $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
                            ->select('users.*', 'friends.user_id as pivot_user_id', 'friends.friend_id as pivot_friend_id');
        // Second part of the union: friends where the user is the friend_id
        $friendsAsFriend = $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
                                ->select('users.*', 'friends.friend_id as pivot_user_id', 'friends.user_id as pivot_friend_id');
        return $friendsAsUser->union($friendsAsFriend->getQuery()); // Using getQuery to maintain the query builder instance
    }
    
    public function rls_conversations() {
        return Conversation::where('user1_id', $this->id)
                           ->orWhere('user2_id', $this->id);
    }

    public function rls_messages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function rls_collections() {
        return $this->hasMany(Collection::class);
    }
    
    public function rls_likedUsers() {
        return $this->belongsToMany(User::class, 'user_likes', 'liker_id', 'liked_id');
    }

    public function rls_reportedItems() //!not used
    {
        return $this->belongsToMany(Item::class, 'item_user_report')
            ->withPivot('reason')
            ->withTimestamps();
    }

    public function rls_reports()       //!not used
    {
        return $this->hasMany(Report::class);
    }

    public function rls_setFollow() {
        return $this->belongsToMany(User::class, 'user_followings', 'follower_id', 'following_id');
    }
    
    public function rls_getFollowing() {
        return $this->belongsToMany(User::class, 'user_followings', 'follower_id', 'following_id');
    }

    public function rls_getFollowers() {
        return $this->belongsToMany(User::class, 'user_followings', 'following_id', 'follower_id');
    }

    public function rls_saveItem(): BelongsToMany {
        return $this->belongsToMany(Item::class, 'user_saves', 'user_id', 'item_id');
    }

    public function rls_sentFriendRequests() {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function rls_receivedFriendRequests() {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function rls_friendRequested()       //!not used
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'receiver_id', 'sender_id')
            ->wherePivot('status', 1) // Assuming 'status' column exists for accepted requests
            ->withPivot('created_at', 'updated_at'); // Include pivot timestamps if needed
    }
    public function rls_isFriendWith(User $user) {
        $friendIds = $this->rls_friends()->pluck('id');
        return $friendIds->contains($user->id);
    }

    public function rls_likedByUsers() {
        return $this->belongsToMany(User::class, 'user_likes', 'liked_id', 'liker_id');
    }

    public function rls_usersILiked() {
        return $this->belongsToMany(User::class, 'user_likes', 'liker_id', 'liked_id');
    }

    public function rls_blocking() {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id');
    }

    public function rls_blockers() {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'blocker_id');
    }
}
