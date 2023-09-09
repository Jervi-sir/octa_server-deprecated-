<?php

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

function getItem($item)
{
  $result = [
    'id' => $item->id,
    'shop_id' => $item->shop_id,
    'shop_image' => $item->shop->shop_image,
    //'shop_image' => imageUrl('shops', $item->shop->shop_image),
    'name' => $item->name,
    'details' => $item->details,
    'sizes' => $item->sizes,
    'stock' => $item->stock,
    'price' => $item->price,
    'item_type_id' => $item->item_type_id,
    'gender_id' => $item->gender_id,
    'search' => $item->search,
    'images' => json_decode($item->images),
    //'images' => imageToArray($item->images->pluck('url')->toArray()),
    'isSaved' => Auth::user() ? $item->savedByUsers->contains(Auth::user()->id) : null,
  ];
  return $result;
}

function getShop($shop)
{
  $result = [
    'shop_name' => $shop->shop_name,
    'shop_image' => imageUrl('shops', $shop->shop_image),
    'details' => $shop->details,
    'contacts' => $shop->contacts,
    'location' => $shop->location,
    'map_location' => $shop->map_location,
    'nb_followers' => $shop->nb_followers,
    'nb_likes' => $shop->nb_likes,
    'wilaya_name' => $shop->wilaya_name,
  ];

  return $result;
}

function imageToArray($images)
{
  if (count($images) == 0) {
    return [[
      "image" => "https://images.unsplash.com/photo-1455620611406-966ca6889d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1130&q=80"
    ]];
  }
  foreach ($images as &$item) {
    $item = ["image" => imageUrl('items', $item)];
  }
  return $images;
}

function imageUrl($source, $image)
{
  return "http://192.168.1.106:8000/" . $source . '/' . $image;
}

function getGenderId($genders) 
{
  $list = '';
  
  foreach ($genders as $gender) {
    if($gender == 'male') {
      $value = 1; 
    } else if ($gender == 'female') {  
      $value = 2;
    }
  
    $list .= $value . ', ';
  }
  
  return $list = rtrim($list, ', ');
}

function getShopAuthDetails($shop) {
  return [
    'username' => $shop->shop_name,
    'profile_picture' => $shop->shop_image,
    'social_media' => $shop->contacts,
    'credit' => 1500,
    'phone_number' => $shop->phone_number,
    'email' => $shop->email,
    'bio' => $shop->details,
    'location' => $shop->location,
    'map_location' => $shop->map_location,
    'nb_followers' => $shop->nb_followers,
    'nb_likes' => $shop->nb_likes,
  ];
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