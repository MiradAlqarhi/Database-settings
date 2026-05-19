<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow($id)
    {
        $currentUserId = auth()->id();

        // check user exists
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // prevent self follow
        if ($currentUserId == $id) {
            return response()->json([
                'message' => 'You cannot follow yourself'
            ], 400);
        }

        // already following
        $alreadyFollowing = Follow::where('follower_id', $currentUserId)
            ->where('following_id', $id)
            ->exists();

        if ($alreadyFollowing) {
            return response()->json([
                'message' => 'Already following this user'
            ], 400);
        }

        Follow::create([
            'follower_id' => $currentUserId,
            'following_id' => $id,
        ]);

        return response()->json([
            'message' => 'Followed successfully'
        ]);
    }

    public function unfollow($id)
    {
        $follow = Follow::where('follower_id', auth()->id())
            ->where('following_id', $id)
            ->first();

        if (!$follow) {
            return response()->json([
                'message' => 'You are not following this user'
            ], 400);
        }

        $follow->delete();

        return response()->json([
            'message' => 'Unfollowed successfully'
        ]);
    }

    public function following()
    {
        $users = User::whereIn(
            'id',
            Follow::where('follower_id', auth()->id())
                ->pluck('following_id')
        )->paginate(10);

        return response()->json($users);
    }

    public function followers()
    {
        $users = User::whereIn(
            'id',
            Follow::where('following_id', auth()->id())
                ->pluck('follower_id')
        )->paginate(10);

        return response()->json($users);
    }

    public function isFollowing($id)
    {
        $isFollowing = Follow::where('follower_id', auth()->id())
            ->where('following_id', $id)
            ->exists();

        return response()->json([
            'is_following' => $isFollowing
        ]);
    }
}