<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'age'    => 'required|integer',
            'gender' => 'required|string',
            'game'   => 'required|string',
            'image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('players_images', 's3');
            $imageUrl = Storage::disk('s3')->url($path);
        }

        $player = Player::create([
            'name'   => $request->name,
            'age'    => $request->age,
            'gender' => $request->gender,
            'game'   => $request->game,
            'image'  => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Success! Player saved.',
            'player'  => $player
        ]);
    }
}