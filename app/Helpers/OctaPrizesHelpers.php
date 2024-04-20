<?php
use App\Models\ItemType;
use App\Models\ProductType;
use Carbon\Carbon;

function OP_getShop($shop)
{
    $auth = auth()->user();
    $isCollected = false;

    if ($auth) {
        // Check if the shop is in any of the user's collections
        $isCollected = $auth->rls_collections()
            ->whereHas('rls_shops', function ($query) use ($shop) {
                $query->where('id', $shop->id);  // or make it to id instead of shop_id
            })
            ->exists();
    }


    $result = [
        'id' => $shop->id,
        'shop_name' => $shop->shop_name,
        //'shop_image' => imageUrl('shops', $shop->shop_image),
        'shop_image' => $shop->shop_image,
        'details' => $shop->details,
        'contacts' => json_decode($shop->contacts),
        //'location' => $shop->location,
        'map_location' => $shop->map_location,
        'nb_followers' => $shop->nb_followers,
        'nb_likes' => $shop->nb_likes,
        'wilaya_name' => $shop->wilaya_name,
        'wilaya_code' => $shop->wilaya_code,
        'isFollowed' => $auth && !isAuthShop() ? $shop->rls_followedByUser->contains($auth->id) : null,
        'isCollected' => $isCollected,
    ];

    return $result;
}
function OP_getFriendToSendTo($friend)
{
    return [
        'id' => $friend->id,
        'username' => $friend->username,
        'profile_image' => $friend->profile_images ? $friend->profile_images[0] : null,
    ];
}
function OP_getProfile($user)
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

function OP_getMyProfile($user = null) {

    $auth = $user === null ? auth()->user() : $user;
    return array_merge(OP_getProfile($auth), [
        'bio' => $auth->bio,
        'contacts' => $auth->contacts,
        'nb_likes' => 0, // Adjust this according to your actual logic
        'nb_friends' => $auth->rls_friends()->count(),
        'isPremium' => $auth->isPremium,
    ]);
}

function OP_getItem($item)
{
    $auth = auth()->user();
    $result = [
        'id' => $item->id,
        'name' => $item->name,
        'details' => $item->details,
        'sizes' => $item->sizes,
        'stock' => $item->stock,
        'price' => $item->price,
        'genders' => $item->genders,
        'search' => $item->keywords,
        'images' => imageToArray(json_decode($item->images)), //'images' => imageToArray($item->images->pluck('url')->toArray()), imageToArray
        'isSaved' => $auth && !isAuthShop() ? $item->rls_savedByUsers->contains($auth->id) : null,
        'keywords' => $item->keywords,
        'isActive' => $item->isActive,
        'posted_since' => $item->last_reposted,
        'category' => $item->rls_item_type,
        'shop' => OP_getShop($item->rls_shop),
    ];
    return $result;
}
