<?php

use Illuminate\Support\Facades\Auth;

function getItem($item) {
  $result = [
    'id' => $item->id,
    'shop_id' => $item->shop_id,
    'shop_image' => imageUrl('shops', $item->shop->shop_image),
    'name' => $item->name,
    'details' => $item->details,
    'sizes' => $item->sizes,
    'stock' => $item->stock,
    'price' => $item->price,
    'item_type_id' => $item->item_type_id,
    'gender_id' => $item->gender_id,
    'search' => $item->search,
    'images' => imageToArray($item->images->pluck('url')->toArray()),
    'isSaved' => $item->savedByUsers->contains(Auth::user()->id),  
  ];
  return $result;
}

function getShop($shop) {
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

function imageToArray($images) {
  if(count($images) == 0) {
      return [[
          "image" => "https://images.unsplash.com/photo-1455620611406-966ca6889d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1130&q=80"
      ]];
  }
  foreach ($images as &$item) {
      $item = ["image" => imageUrl('items', $item)];
  }
  return $images;
}

function imageUrl($source, $image) {
  return "http://192.168.1.106:8000/" . $source . '/' . $image;
}