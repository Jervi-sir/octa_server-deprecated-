<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest() {
        $items = Item::inRandomOrder()->paginate(10);
        foreach($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }
        
        return response()->json([
            'data' => $data['items'],
            'next' => $items->nextPageUrl(),
        ]);
    }

    public function search($keywords) {
        $keyword_array = explode (",", $keywords); 

        $items = Item::where(function($query) use ($keyword_array) {
                foreach($keyword_array as $keyword) {
                    $query->where('search', 'like', '%' . $keyword . '%');
                }
            })
            ->paginate(10);

        foreach($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }
        
        return response()->json([
            'data' => $data['items'],
            'next' => $items->nextPageUrl(),
        ]);
    }

}
