<?php
use App\Models\ItemType;
use App\Models\ProductType;
use Carbon\Carbon;

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
        'images' => imageToArray(json_encode($item->images)), //'images' => imageToArray($item->images->pluck('url')->toArray()),
        'isSaved' => $auth && !isAuthShop() ? $item->rls_savedByUsers->contains($auth->id) : null,
        'keywords' => $item->keywords,
        'isActive' => $item->isActive,
        'posted_since' => $item->last_reposted,
        'category' => $item->rls_item_type,
        'shop' => getMyShop($item->rls_shop),
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
