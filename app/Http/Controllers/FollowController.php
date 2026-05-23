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

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($currentUserId == $id) {
            return response()->json([
                'message' => 'You cannot follow yourself'
            ], 400);
        }

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
    $followingIds = Follow::where('follower_id', auth()->id())
        ->pluck('following_id');

    $users = User::whereIn('id', $followingIds)->get();

    $result = $users->map(function ($user) {
        if ($user->type === 'scout') {
            $scout = $user->scout;
            return [
                'id' => $user->id,
                'type' => 'scout',
                'name' => $scout?->name,
                'organization' => $scout?->organization_name,
                'avatar_url' => $user->avatar
                    ? Storage::disk('s3')->url($user->avatar)
                    : null,
            ];
        } else {
            $player = $user->player;
            return [
                'id' => $user->id,
                'type' => 'player',
                'name' => $player?->name,
                'game' => $player?->game,
                'win_rate' => $player?->win_rate,
                'tournaments_count' => $player?->tournaments()->count(),
                'avatar_url' => $user->avatar
                    ? Storage::disk('s3')->url($user->avatar)
                    : null,
            ];
        }
    });

    return response()->json($result);
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