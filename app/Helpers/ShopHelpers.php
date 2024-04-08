<?php

function getShop($shop)
{
    $auth = auth()->user();
    $isCollected = false;

    if ($auth) {
        // Check if the shop is in any of the user's collections
        $isCollected = $auth->rls_collections()
            ->whereHas('shops', function ($query) use ($shop) {
                $query->where('shop_id', $shop->id);
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

function getShopAuthDetails($shop)
{
    return [
    ];
}

function getMyShop($shop = null)
{
    $shop = $shop != null ? $shop : auth()->user();
    return [
        'username' => $shop->username,
        'phone_number' => $shop->phone_number,
        'shop_name' => $shop->shop_name,
        'shop_image' => $shop->shop_image,
        'bio' => $shop->bio,
        'contacts' => $shop->contacts,
        'wilaya_code' => $shop->wilaya_code,
        'wilaya_name' => $shop->wilaya_name,
        'map_location' => $shop->map_location,
        'nb_followers' => $shop->nb_followers,
        'nb_likes' => $shop->nb_likes,
        'total_items' => $shop->rls_items->count(),
        'created_at' => $shop->created_at,
    ];
}

function getUserAsShop($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'bio' => $user->bio,
        'username' => $user->username,
        'profile_image' => $user->profile_images ? $user->profile_images[0] : null,
    ];
}

function getProductAsShop($product) {
    $shop = auth()->user();
    $images = json_decode($product->images);
    $thumbnail = is_array($images) && !empty($images) ? $images[0] : null;

    return [
        'id' => $product->id,
        'name' => $product->name,
        'thumbnail' => $thumbnail,      //!empty($product->images) ? json_decode($product->images)[0] : null, // or provide a default image URL
        'price' => $product->price,
        'product_type_id' => $product->product_type_id,
        'is_expired' => checkIfItemIsExpired($product->last_reposted),
        'last_reposted' => $product->last_reposted,
        'created_at' => $product->created_at,
        'shop_name' => $shop->shop_name,
        'shop_image' => $shop->shop_image,
        'isActive' => $product->isActive,
    ];
}