<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\DB;


class PlayerController extends Controller
{
    public function store(Request $request)
    {
        $player = Player::create([
            'name'   => $request->name,
            'age'    => $request->age,
            'gender' => $request->gender,
            'game'   => $request->game,
             'user_id' => auth()->id(),
        ]);

       $user = auth()->user();

    if ($user) {
        $user->profile_completed = true;
        $user->save();
    }

        return response()->json([
            'message' => 'Success! Player saved.',
            'player'  => $player
        ], 201);
    }
  
    public function index(Request $request)
{ 

  $genderStats = Player::select('gender', DB::raw('count(*) as total'))
  ->where('game', $game)
  ->groupBy('gender')
  ->get();

  $ageStats = Player::select(
    DB::raw("
        CASE
            WHEN age BETWEEN 10 AND 18 THEN '10-18'
            WHEN age BETWEEN 19 AND 25 THEN '19-25'
            WHEN age BETWEEN 26 AND 35 THEN '26-35'
            WHEN age BETWEEN 36 AND 50 THEN '36-50'
        END as age_group
    "),
    DB::raw('count(*) as total')
)
->where('game', $game)
->groupBy('age_group')
->get();

$averageAge = Player::where('game', $game)
    ->avg('age');

$medalStats = Player::select(DB::raw('SUM(gold) as total_gold, SUM(silver) as total_silver, SUM(bronze) as total_bronze'));

    return response()->json([
        'gender_stats' => $genderStats,
        'age_stats' => $ageStats,
        'medal_stats' => $medalStats,
        'average' => $averageAge
    ]);
}
}