<?php

namespace App\Http\Controllers\Api\OP;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class OpItemController extends Controller
{
    public function reportItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'reasons' => 'nullable',
        ]);
        
        $data = $request->all();
        $user = auth()->user();
        $item = Item::findOrFail($data['item_id']);

        if ($item->rls_reports()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already reported this item.']);
        }

        $item->increment('nb_reports');

        $item->rls_reports()->create([
            'user_id' => $user->id,
            'reasons' => $request->input('reasons'), // You can get the reason from the request
        ]);

        $item->save();

        return response()->json(['message' => 'Item reported successfully.']);
    }

    public function saveThisItem(Request $request)
    {
        $request->validate([
            'item_id'   => 'required',
        ]);

        $data = $request->all();
        $user = auth()->user();

        if ($user->rls_saveItem()->where('item_id', $data['item_id'])->exists()) {
            return response()->json([
                'message' => 'Item already saved'
            ], 422);
        }

        $user->rls_saveItem()->attach($data['item_id']);
        return response()->json([
            'message' => 'Item saved'
        ], 200);
    }

    public function unSaveThisItem(Request $request)
    {
        $request->validate([
            'item_id'   => 'required',
        ]);

        $data = $request->all();
        $user = auth()->user();

        $user->rls_saveItem()->detach($data['item_id']);
        return response()->json([
            'message' => 'item unSaved'
        ], 200);
    }
}
