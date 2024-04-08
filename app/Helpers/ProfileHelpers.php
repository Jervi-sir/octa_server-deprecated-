<?php

function isAuthShop() {
    $user = auth() -> user();
    $isShop =$user->getTable() === 'shops';
    return $isShop;

}
function getFriendToSendTo($friend)
{
    return [
        'id' => $friend->id,
        'username' => $friend->username,
        'profile_image' => $friend->profile_images ? $friend->profile_images[0] : null,
    ];
}
function getProfile($user)
{
    $auth = auth()->user();

    $isMyAccount = $auth && $auth->id === $user->id;

    $isFriend = false;
    $followingStatus = null; // Default status
    $isBlocked = false; // Initial assumption

    if ($auth && !$isMyAccount) {
        $isBlocking = $auth->rls_blocking()->where('blocked_id', $user->id)->exists();
        $isBlockedBy = $user->rls_blockers()->where('blocker_id', $auth->id)->exists();
        $isBlocked = $isBlocking || $isBlockedBy; // Either condition sets isBlocked to true

        if (!$isBlocked) {
            // Check if they are friends
            $isFriend = $auth->rls_friends()->contains('id', $user->id);
            if ($isFriend) {
                $followingStatus = 'friends';
            } else {
                // Check for sent friend requests
                $sentRequest = $auth->rls_sentRequests()->where('receiver_id', $user->id)->exists();
                if ($sentRequest) {
                    $followingStatus = 'request_sent';
                } else {
                    // Check for received friend requests
                    $receivedRequest = $auth->rls_receivedRequests()->where('sender_id', $user->id)->exists();
                    if ($receivedRequest) {
                        $followingStatus = 'request_received';
                    }
                }
            }
        }
    }
    $numberOfFriends = $user->rls_friends()->count();
    $numberOfLikes = $user->rls_likedByUsers()->count();

    return [
        'id' => $user->id,
        'name' => $user->name,
        'bio' => $user->bio,
        'username' => $user->username,
        'profile_image' => $user->profile_images ? $user->profile_images[0] : null,
        'isFollowed' => $isFriend,
        'followingStatus' => $followingStatus,
        'isLiked' => $auth ? $user->rls_likedByUsers->contains('id', $auth->id) : false,
        'isMyAccount' => $isMyAccount,
        'number_friends' => $numberOfFriends,
        'number_likes' => $numberOfLikes,
        'isBlocked' => $isBlocked,
    ];
}

function getMyProfile($user = null) {

    $auth = $user === null ? auth()->user() : $user;
    return array_merge(getProfile($auth), [
        'bio' => $auth->bio,
        'contacts' => $auth->contacts,
        'nb_likes' => 0, // Adjust this according to your actual logic
        'nb_friends' => $auth->rls_friends()->count(),
        'isPremium' => $auth->isPremium,
    ]);
}
