<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    public function saveItem($itemId)
    {
        $user = Auth::user();

        if ($user->savedItems()->where('item_id', $itemId)->exists()) {
            return response()->json(['message' => 'Item already saved'], 422);
        }

        $user->savedItems()->attach($itemId);
        return response()->json('saved item', 200);
    }

    public function unSaveItem($itemId)
    {
        $user = Auth::user();
        $user->savedItems()->detach($itemId);
        return response()->json('unSaved item', 200);
    }
}
