<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\DB;
use App\Models\SocialMedia;//اضفت ذا
use App\Models\Follow;
class PlayerController extends Controller
{
    public function store(Request $request)
    {
        $player = Player::create([
            'name'   => $request->name,
            'age'    => $request->age,
            'gender' => $request->gender,
            'game'   => $request->game,
           'contact_email' => $request->contact_email ?? null,
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
//اضفت ذا
public function show()
{
    $player = Player::with(['user', 'socialMedia'])
        ->where('user_id', auth()->id())
        ->latest()
        ->first();
       $followersCount = Follow::where('following_id', auth()->id())->count();
       $player->followersCount = $followersCount;

    return response()->json($player);

}
//اضفت ذا
public function updateProfile(Request $request)
{
    $player = Player::where('user_id', auth()->id())->latest()->first();

    if (!$player) {
        return response()->json([
            'message' => 'Player profile not found'
        ], 404);
    }

    $user = auth()->user();

    if (!empty($request->email)) {
        $user->email = $request->email;
        $user->save();
    }

    $player->update([
        'name' => $request->name ?? $player->name,
        'age' => $request->age ?? $player->age,
        'game' => $request->game ?? $player->game,
        'contact_email' => $request->contact_email ?? $player->contact_email,
    ]);

    SocialMedia::where('user_id', auth()->id())->delete();

    foreach (['instagram', 'x', 'snapchat', 'discord'] as $platform) {
        if (!empty($request->$platform)) {
            SocialMedia::create([
                'platform' => $platform,
                'url' => $request->$platform,
                'user_id' => auth()->id(),
            ]);
        }
    }

    return response()->json([
        'message' => 'Profile updated',
        'player' => $player
    ]);
}
}