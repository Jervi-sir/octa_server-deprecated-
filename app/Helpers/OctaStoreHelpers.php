<?php
use App\Models\ItemType;
use App\Models\ProductType;
use Carbon\Carbon;

function isAuthShop() {
    $user = auth() -> user();
    $isShop =$user->getTable() === 'shops';
    return $isShop;
}

function getItem($item)
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
        'shop' => OS_getMyShop($item->rls_shop),
    ];
    return $result;
}


function checkIfItemIsExpired($createdAt)
{
    $currentDate = Carbon::now();
    $itemCreatedAt = new Carbon($createdAt); // Assume $createdAt is something like '2023-09-01 12:34:56'

    // Carbon::diffInDays() will give you the difference in days between two dates
    $daysOld = $currentDate->diffInDays($itemCreatedAt);

    if ($daysOld >= 7) {
        return true; // Item is 7 days old or more
    } else {
        return false;
    }
}



function getShopAuthDetails($shop)
{
    return [
    ];
}

function OS_getMyShop($shop = null)
{
    $shop = $shop !== null ? $shop : auth()->user();
    $contacts = json_decode($shop->contacts, true);
    $id = 1;
   
    $processedContacts = [];
    if (is_array($contacts)) {
        $processedContacts = array_map(function ($contact) use (&$id) {
            return array_merge(['id' => $id++], $contact);
        }, $contacts);
        $processedContacts = array_reverse($processedContacts);
    }

    return [
        'id' => $shop->id,
        'username' => $shop->username,
        'phone_number' => $shop->phone_number,
        'shop_name' => $shop->shop_name,
        'shop_image' => $shop->shop_image,
        'bio' => $shop->bio,
        'contacts' => $processedContacts,
        'wilaya_code' => $shop->wilaya_code,
        'wilaya_name' => $shop->wilaya_name,
        'wilaya_created_at' => $shop->wilaya_created_at,
        'map_location' => $shop->map_location,
        'nb_followers' => $shop->nb_followers,
        'nb_likes' => $shop->nb_likes,
        'total_items' => $shop->rls_items->count(),
        'created_at' => $shop->created_at,
    ];
}


function OS_getUserAsShop($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'bio' => $user->bio,
        'username' => $user->username,
        'profile_image' => $user->profile_images ? $user->profile_images[0] : null,
    ];
}

function OS_getProductAsShop($product) {
    $images = imageToArray(json_decode($product->images));
    $thumbnail = is_array($images) && !empty($images) ? $images[0] : null;

    return [
        'id' => $product->id,
        'item_type_id' => $product->item_type_id,
        'item_type_name' => strtolower(ItemType::find($product->item_type_id)->name),
        'name' => $product->name,
        'details' => $product->details,      //!empty($product->images) ? json_decode($product->images)[0] : null, // or provide a default image URL
        'price' => $product->price,
        'genders' => $product->genders,
        'thumbnail' => $thumbnail,      //!empty($product->images) ? json_decode($product->images)[0] : null, // or provide a default image URL
        'images' => $images,      //!empty($product->images) ? json_decode($product->images)[0] : null, // or provide a default image URL
        'keywords' => $product->keywords,
        'isActive' => $product->isActive,
        'last_reposted' => $product->last_reposted,
        'nb_reports' => $product->nb_reports,
        'is_expired' => checkIfItemIsExpired($product->last_reposted),
        'created_at' => $product->created_at,
    ];
}