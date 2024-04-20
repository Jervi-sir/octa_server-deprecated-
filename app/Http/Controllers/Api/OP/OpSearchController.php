<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemType;
use Illuminate\Http\Request;

class OpSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'keywords'   => 'nullable',
            'category_name'   => 'nullable',
            'gender_name'   => 'nullable',
            'wilaya_code'   => 'nullable',
        ]);

        $auth = auth()->user();

        $data = $request->all();
        $keyword_array = !empty($data['keywords']) ? explode(",", $data['keywords']) : [];

        $items = Item::query();
    
        // Only apply keyword filtering if keywords are provided
        if (!empty($keyword_array)) {
            $items = $items->where(function ($query) use ($keyword_array) {
                foreach ($keyword_array as $keyword) {
                    if ($keyword) {
                        $query->where('keywords', 'like', '%' . $keyword . '%');
                    }
                }
            });
        }

        if (!empty($data['category_name'])) {
            $category_id = ItemType::where('name', 'like', '%' . $data['category_name'] . '%')->first()->id;
            $items = $items->where('item_type_id', $category_id);
        }

        if (!empty($data['gender_name'])) {
            if(strtolower($data['gender_name']) != 'all')
            $items = $items->where('genders', 'like', '%' . $data['gender_name'] . '%');
        }
    
        if (!empty($data['wilaya_code'])) {
            // Assuming you have a way to relate items with wilaya_code
            $items = $items->where('wilaya_code', $data['wilaya_code']);
        }

        $items = $items->orderBy('id', 'DESC')->paginate(10);
        
        $data['items'] = [];
        foreach ($items as $index => $item) {
            $data['items'][$index] = OP_getItem($item);
        }

        $nextPage = null;
        if ($items->nextPageUrl()) {
            $nextPage = $items->currentPage() + 1;
        }

        return response()->json([
            'user_status' => $auth ? 'You are authenticated' : 'You are NOT authenticated',
            'next_url' => $items->nextPageUrl(),
            'next_page' => $nextPage,
            'last' => $items->lastPage(),
            'items' => $data['items'],
        ], 200);
    }
}
