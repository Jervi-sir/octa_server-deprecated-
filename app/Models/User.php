<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'phone_number', 'email', 'password', 'name', 'username', 'bio', 'profile_images', 'contacts', 
        'nb_likes', 'nb_friends', 'isPremium', 'credit', 'wilaya_id', 'wilaya_created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'isPremium' => 'boolean',
        'nb_likes' => 'integer',
        'nb_friends' => 'integer',
        'credit' => 'integer',
        'email_verified_at' => 'datetime',
        'contacts' => 'array',
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Shops
    |--------------------------------------------------------------------------
    */
    public function rls_collections() {
        return $this->hasMany(Collection::class);
    }
    /*
    |--------------------------------------------------------------------------
    | Item
    |--------------------------------------------------------------------------
    */
    public function rls_reports()       //!not used
    {
        return $this->hasMany(Report::class);
    }
    public function rls_saveItem(): BelongsToMany {
        return $this->belongsToMany(Item::class, 'user_saves', 'user_id', 'item_id');
    }
    public function rls_reportedItems() //!not used
    {
        return $this->belongsToMany(Item::class, 'item_user_report')
            ->withPivot('reason')
            ->withTimestamps();
    }
    /*
    |--------------------------------------------------------------------------
    | Conversation
    |--------------------------------------------------------------------------
    */
    public function rls_conversations() {
        return Conversation::where(function (Builder $query) {
            $auth = auth()->user();
            $query->where('user1_id', $auth->id)
                ->orWhere('user2_id', $auth->id);
        })->orderBy('updated_at', 'desc');
    }
    public function rls_messages() {
        return $this->hasMany(Message::class, 'sender_id');
    }
    /*
    |--------------------------------------------------------------------------
    | Block
    |--------------------------------------------------------------------------
    */
    public function rls_blocking() {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id');
    }
    public function rls_blockers() {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'blocker_id');
    }
    // Users that this user has blocked
    public function rls_usersIBlocked()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id')
                    ->withTimestamps()
                    ->orderByDesc('blocks.created_at');
    }
    // Users who have blocked this user
    public function rls_usersBlockingMe()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'blocker_id')
                    ->withTimestamps()
                    ->orderByDesc('blocks.created_at');
    }
    public function rls_blockExists($user) 
    {
        $isBlocking = $this->rls_usersIBlocked()->where('blocked_id', $user->id)->exists();
        $isBlockedBy = $this->rls_usersBlockingMe()->where('blocker_id', $user->id)->exists();
        return $isBlocking || $isBlockedBy;
    }

    public function rls_haveIBlockedHim($user)
    {
        return $this->rls_usersIBlocked()->where('blocked_id', $user->id)->exists();
    }
    public function rls_hasHeBlockedMe($user)
    {
        return $this->rls_usersBlockingMe()->where('blocker_id', $user->id)->exists();
    }
    /*
    |--------------------------------------------------------------------------
    | Likes
    |--------------------------------------------------------------------------
    */
    public function rls_usersILike()
    {
        return $this->belongsToMany(User::class, 'user_likes', 'liker_id', 'liked_id')
                    ->withTimestamps()
                    ->orderByDesc('user_likes.created_at');
    }
    // Relationship for users that have liked this user
    public function rls_usersWhoLikeMe()
    {
        return $this->belongsToMany(User::class, 'user_likes', 'liked_id', 'liker_id')
                    ->withTimestamps()
                    ->orderByDesc('user_likes.created_at');
    }
    public function rls_likedUsers() {
        return $this->belongsToMany(User::class, 'user_likes', 'liker_id', 'liked_id');
    }
    /*
    |--------------------------------------------------------------------------
    | Following
    |--------------------------------------------------------------------------
    */
    public function rls_setFollow() {
        return $this->belongsToMany(User::class, 'user_followings', 'follower_id', 'following_id');
    }
    public function rls_getFollowing() {
        return $this->belongsToMany(User::class, 'user_followings', 'follower_id', 'following_id');
    }
    public function rls_getFollowers() {
        return $this->belongsToMany(User::class, 'user_followings', 'following_id', 'follower_id');
    }
    /*
    |--------------------------------------------------------------------------
    | Request
    |--------------------------------------------------------------------------
    */
    public function rls_sentRequests() {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }
    public function rls_receivedRequests() {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }
    /*
    |--------------------------------------------------------------------------
    | Friends
    |--------------------------------------------------------------------------
    */
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
        $friendIds = $this->rls_friends()->pluck('friends.friend_id');  //id instead of friends.friend_id
        return $friendIds->contains($user->id);
    }
    public function rls_friends_old() { //! Deprecated
        // Friends where the user is the user_id
        $friendsAsUser = $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->selectRaw('users.*, friends.user_id as pivot_user_id, friends.friend_id as pivot_friend_id');
        // Friends where the user is the friend_id
        $friendsAsFriend = $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
            ->selectRaw('users.*, friends.friend_id as pivot_user_id, friends.user_id as pivot_friend_id');
        // Use qualified column name in the where clause to avoid ambiguity
        return $friendsAsUser->union(
            $friendsAsFriend->getQuery()
        );
    }
    public function rls_friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')->withTimestamps();
    }
    
    public function rls_friendOf()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')->withTimestamps();
    }
    
    public function rls_allFriends($perPage = 10, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        
        // Only paginate one relationship for clear pagination handling
        return $this->rls_friends()->paginate($perPage, ['*'], 'page', $page);
    }
}
