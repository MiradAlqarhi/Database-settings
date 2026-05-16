<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    public function store(Request $request)
    {
        // Save data to the database
        $player = Player::create([
            'name'   => $request->name,
            'age'    => $request->age,
            'gender' => $request->gender,
            'game'   => $request->game,
        ]);

        return response()->json([
            'message' => 'Success! Player saved.',
            'player'  => $player
        ], 201);
    }
}
