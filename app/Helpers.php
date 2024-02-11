<?php

use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

function getItem($item)
{
  $auth = auth()->user();
  $result = [
    'id' => $item->id,
    'shop_id' => $item->shop_id,
    'shop_name' => $item->shop->shop_name,
    'shop_image' => $item->shop->shop_image,
    'map_location' => $item->shop->map_location,
    //'shop_image' => imageUrl('shops', $item->shop->shop_image),
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
    'isSaved' => $auth ? $item->savedByUsers->contains($auth->id) : null,
    'shop' => getShop($item->shop),
    'posted_since' => $item->last_reposted
  ];
  return $result;
}

function getShop($shop)
{
  $auth = auth()->user();
  $isCollected = false;

  if ($auth) {
    // Check if the shop is in any of the user's collections
    $isCollected = $auth->collections()
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
    'isFollowed' => $auth ? $shop->followedByUser->contains($auth->id) : null,
    'isCollected' => $isCollected,
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
  $list = [];
  
  foreach ($genders as $gender) {
    if($gender == 'male') {
      $value = 1; 
    } else if ($gender == 'female') {
      $value = 2;
    }
    $list[] = $value;
  }
  
  sort($list); // Sorts the array of gender IDs
  
  return implode(', ', $list);  // Converts the sorted array back to a string
}

function getGenderNames($data) {
  $text = str_replace("1", "male", $data);
  $text = str_replace("2", "female", $text);
  return $text;
}


// Define mapping 
function getShopAuthDetails($shop) {
  return [
    'username' => $shop->username,
    'phone_number' => $shop->phone_number,
    'shop_name' => $shop->shop_name,
    'shop_image' => $shop->shop_image,
    'bio' => $shop->bio,
    'contacts' => $shop->contacts,
    'map_location' => $shop->map_location,
    'nb_followers' => $shop->nb_followers,
    'nb_likes' => $shop->nb_likes,
    'wilaya_name' => $shop->wilaya_name,
    'wilaya_code' => $shop->wilaya_code,
    'created_at' => $shop->created_at,
    'total_items' => $shop->items->count(),
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

function removeNullsFromStart($array) {
  $newArray = [];
  $foundNonNull = false;

  foreach ($array as $item) {
      if (!$foundNonNull && is_null($item)) {
          continue;
      }
      
      $foundNonNull = true;
      $newArray[] = $item;
  }

  // Fill the rest of the array with null values to preserve length
  while (count($newArray) < count($array)) {
      $newArray[] = null;
  }

  return $newArray;
}


function saveSingleImage($base64Image) {
  $imageName = uniqid() . '.png';
  $imagePath = 'public/images/' . $imageName;
  Storage::put($imagePath, base64_decode($base64Image));
  $imagePath = env('API_URL') . '/storage/images/' . $imageName;
  return $imagePath;
}

function getFriendToSendTo($friend) {
  return [
    'id' => $friend->id,
    'username' => $friend->username,
    'profile_image' => $friend->profile_images ? $friend->profile_images[0] : null,
  ];
}

function getProfile($user) {
  $auth = auth()->user();

  $isMyAccount = $auth && $auth->id === $user->id;

  $isFriend = false;
  $followingStatus = null; // Default status
  $isBlocked = false; // Initial assumption

  if ($auth && !$isMyAccount) {
    $isBlocking = $auth->blocking()->where('blocked_id', $user->id)->exists();
    $isBlockedBy = $user->blockers()->where('blocker_id', $auth->id)->exists();
    $isBlocked = $isBlocking || $isBlockedBy; // Either condition sets isBlocked to true

    if(!$isBlocked) {
      // Check if they are friends
      $isFriend = $auth->friends()->contains('id', $user->id);
      if ($isFriend) {
          $followingStatus = 'friends';
      } else {
          // Check for sent friend requests
          $sentRequest = $auth->sentRequests()->where('receiver_id', $user->id)->exists();
          if ($sentRequest) {
              $followingStatus = 'request_sent';
          } else {
              // Check for received friend requests
              $receivedRequest = $auth->receivedRequests()->where('sender_id', $user->id)->exists();
              if ($receivedRequest) {
                  $followingStatus = 'request_received';
              }
          }
      }
    }
  }
  $numberOfFriends = $user->friends()->count();
  $numberOfLikes = $user->likedByUsers()->count();

  return [
    'id' => $user->id,
    'name' => $user->name,
    'bio' => $user->bio,
    'username' => $user->username,
    'profile_image' => $user->profile_images ? $user->profile_images[0] : null,
    'isFollowed' => $isFriend,
    'followingStatus' => $followingStatus,
    'isLiked' => $auth ? $user->likedByUsers->contains('id', $auth->id) : false,
    'isMyAccount' => $isMyAccount,
    'number_friends' => $numberOfFriends, 
    'number_likes' => $numberOfLikes, 
    'isBlocked' => $isBlocked, 
  ];
}
