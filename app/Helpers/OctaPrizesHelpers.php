<?php
use App\Models\ItemType;
use App\Models\ProductType;
use App\Models\Wilaya;
use Carbon\Carbon;

function OP_getShop($shop)
{
    $auth = auth()->user();
    $isCollected = false;
    $nbCollectedAt = 0;

    if ($auth) {
        // Check if the shop is in any of the user's collections
        $collections = $auth->rls_collections()
            ->whereHas('rls_shops', function ($query) use ($shop) {
                $query->where('id', $shop->id);
            });
        
        $isCollected = $collections->exists();
        $nbCollectedAt = $collections->count();  // Count how many collections contain this shop
    }

    $wilaya = Wilaya::find($shop->wilaya_id);

    $result = [
        'id' => $shop->id,
        'shop_name' => $shop->shop_name,
        //'shop_image' => imageUrl('shops', $shop->shop_image),
        'shop_image' => $shop->shop_image,
        'details' => $shop->bio,
        'contacts' => json_decode($shop->contacts),
        //'location' => $shop->location,
        'map_location' => $shop->map_location,
        'nb_followers' => $shop->nb_followers,
        'nb_likes' => $shop->nb_likes,
        'wilaya_name' => $wilaya->name,
        'wilaya_code' => $wilaya->code,
        'isFollowed' => $auth && !isAuthShop() ? $shop->rls_followedByUser->contains($auth->id) : null,
        'isCollected' => $isCollected,
        'nb_collected_at' => $nbCollectedAt
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
    $followingStatus = null;
    $isBlocked = false;

    if ($auth && !$isMyAccount) {
        $isBlocking = $auth->rls_blocking()->where('blocked_id', $user->id)->exists();
        $isBlockedBy = $user->rls_blockers()->where('blocker_id', $auth->id)->exists();
        $isBlocked = $isBlocking || $isBlockedBy;

        if (!$isBlocked) {
            // Check if they are friends
            $isFriend = $auth->rls_friends()->where('users.id', $user->id)->exists();

            $followingStatus = $isFriend ? 'friends' : null;

            if (!$isFriend) {
                $sentRequest = $auth->rls_sentRequests()->where('receiver_id', $user->id)->exists();
                $followingStatus = $sentRequest ? 'request_sent' : null;

                if (!$sentRequest) {
                    $receivedRequest = $auth->rls_receivedRequests()->where('sender_id', $user->id)->exists();
                    $followingStatus = $receivedRequest ? 'request_received' : null;
                }
            }
        }
    }
    $numberOfFriends = $user->rls_friends()->count();
    $numberOfLikes = $user->rls_usersWhoLikeMe()->count();

    $contacts = $user->contacts;
    $id = 1;
    $processedContacts = [];
    if (is_array($contacts)) {
        $processedContacts = array_map(function ($contact) use (&$id) {
            return array_merge(['id' => $id++], $contact);
        }, $contacts);
        $processedContacts = array_reverse($processedContacts);
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'bio' => $user->bio,
        'username' => $user->username,
        'profile_image' => $user->profile_images ? $user->profile_images[0] : null,
        'isFollowed' => $isFriend,
        'followingStatus' => $followingStatus,
        'isLiked' => $auth ? $user->rls_usersWhoLikeMe->contains('id', $auth->id) : false,
        'isMyAccount' => $isMyAccount,
        'isBlocked' => $isBlocked,
        'contacts' => $processedContacts,
        'nb_likes' => $numberOfLikes, //UserLike::where('liked_user_id', $userId)->count(),
        'nb_friends' => $numberOfFriends,
        'isPremium' => $user->isPremium,
    ];
}

function OP_getMyProfile($user = null) {

    $auth = $user === null ? auth()->user() : $user;
    return OP_getProfile($auth);
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
        'isSaved' => $auth ? $item->rls_savedByUsers->contains($auth->id) : null,
        'keywords' => $item->keywords,
        'isActive' => $item->isActive,
        'posted_since' => $item->last_reposted,
        'category' => ItemType::find($item->item_type_id),
        'shop' => OP_getShop($item->rls_shop),
    ];
    return $result;
}
