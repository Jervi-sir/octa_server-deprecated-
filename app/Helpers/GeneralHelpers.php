<?php

function imageToArray($images)
{
    if (count($images) == 0) {
        return [
            [
                "image" => "https://images.unsplash.com/photo-1455620611406-966ca6889d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1130&q=80"
            ]
        ];
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
        if ($gender == 'male') {
            $value = 1;
        } else if ($gender == 'female') {
            $value = 2;
        }
        $list[] = $value;
    }

    sort($list); // Sorts the array of gender IDs

    return implode(', ', $list);  // Converts the sorted array back to a string
}
function getGenderNames($data)
{
    $text = str_replace("1", "male", $data);
    $text = str_replace("2", "female", $text);
    return $text;
}

function removeNullsFromStart($array)
{
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

function saveSingleImage($base64Image)
{
    $imageName = uniqid() . '.png';
    $imagePath = 'public/images/' . $imageName;
    Storage::put($imagePath, base64_decode($base64Image));
    $imagePath = env('API_URL') . '/storage/images/' . $imageName;
    return $imagePath;
}
