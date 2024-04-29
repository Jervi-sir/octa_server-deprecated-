<?php

namespace App\Http\Controllers\Api_old;

use App\Models\Shop;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{




    public function getSavedItems()
    {
        $user = Auth::user();
        $items = $user->savedItems;
        $data['items'] = [];
        foreach ($items as $index => $item) {
            $data['items'][$index] = getItem($item);
        }

        return response()->json([
            'user_status' => Auth::user() ? 'You are authenticated' : 'You are NOT authenticated',
            'items' => $data['items'],
        ], 200);
    }

 

    public function showMyMap(Request $request)
    {
        $request->validate([
            'page'   => 'nullable|numeric',
        ]);
        
        $data = $request->all();
        $user = auth()->user();
        $map = $user->game_map;

        return response()->json([
            'status'    => true,
            'message'   => 'updated successfully',
            'map'      => $map,
        ], 200);  
    }
}
