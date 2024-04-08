<?php
use App\Models\ProductType;
use Carbon\Carbon;

function getItem($item)
{
    $auth = auth()->user();
    $result = [
        'id' => $item->id,
        'shop_id' => $item->shop_id,
        'shop_name' => $item->rls_store->shop_name,
        'shop_image' => $item->rls_store->shop_image,
        'map_location' => $item->rls_store->map_location,
        //'shop_image' => imageUrl('shops', $item->rls_store->shop_image),
        'name' => $item->name,
        'details' => $item->details,
        'sizes' => $item->sizes,
        'stock' => $item->stock,
        'price' => $item->price,
        'category' => ProductType::find($item->product_type_id)->name,
        'category_id' => $item->product_type_id,
        'genders' => $item->genders,
        'search' => $item->keywords,
        'images' => json_decode($item->images),
        'wilaya_code' => ($item->wilaya_code),
        //'images' => imageToArray($item->images->pluck('url')->toArray()),
        'isSaved' => $auth && !isAuthShop() ? $item->rls_savedByUsers->contains($auth->id) : null,
        'keywords' => $item->keywords,
        'isActive' => $item->isActive,
        'shop' => getShop($item->shop),
        'posted_since' => $item->last_reposted
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
