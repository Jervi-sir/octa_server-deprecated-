<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlockController extends Controller
{
    public function blockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $blockerId = Auth::id();
        $blockedId = $request->user_id;

        // Check if already blocked to prevent duplicate entries
        if (!DB::table('blocks')->where('blocker_id', $blockerId)->where('blocked_id', $blockedId)->exists()) {
            DB::table('blocks')->insert([
                'blocker_id' => $blockerId,
                'blocked_id' => $blockedId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'User blocked successfully']);
    }

    public function unblockUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $blockerId = Auth::id();
        $blockedId = $validated['user_id'];

        DB::table('blocks')->where('blocker_id', $blockerId)->where('blocked_id', $blockedId)->delete();

        return response()->json(['message' => 'User unblocked successfully']);
    }

    public function listBlockedUsers(Request $request)
    {
        $request->validate([
            'page' => 'nullable',
        ]);

        $user = Auth::user(); // Get the authenticated user
        // Fetch the users blocked by the authenticated user
        $blockedUsers = $user->blocking()->paginate(10, ['users.id', 'users.name', 'users.username', 'users.profile_images']); // 10 items per page

        $data['users'] = [];
        foreach ($blockedUsers as $key => $user) {
            $data['users'][$key] = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'profile_images' => $user->profile_images ? ($user->profile_images)[0] : null,
                'isBlocked' => true,
            ];
        }

        $nextPage = null; // Default to null when there's no next page
        if ($blockedUsers->hasMorePages()) {
            $nextPage = $blockedUsers->currentPage() + 1;
        }
        
        return response()->json([
            'next_page' => $nextPage,
            'total' => $blockedUsers->total(),
            'users' => $data['users'],
        ]);
    }
}
