<?php

function getItem($item) {
  $result = [
    'id' => $item->id,
    'shop_id' => $item->shop_id,
    'name' => $item->name,
    'details' => $item->details,
    'sizes' => $item->sizes,
    'stock' => $item->stock,
    'price' => $item->price,
    'item_type_id' => $item->item_type_id,
    'gender_id' => $item->gender_id,
    'search' => $item->search,
    'images' => imageToArray($item->images->pluck('url')->toArray()),
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
      $item = ["image" => "http://192.168.1.106:8000/items/" . $item];
  }
  return $images;
}