<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MedalCount;
use App\Models\SocialMedia;//اضفت ذا
use App\Models\Follow;
use App\Models\Scout;
use App\Models\Tournament;

class PlayerController extends Controller
{
    public function store(Request $request)
    {

      $request->validate([
            'name'   => 'required|string|max:255',
            'age'    => 'required|integer',
            'gender' => 'required|string',
            'game'   => 'required|string',
            
        ]);

        $player = Player::create([
            'name'   => $request->name,
            'age'    => $request->age,
            'gender' => $request->gender,
            'game'   => $request->game,
            'user_id' => auth()->id(),
            'contact_email' => $request->contact_email ?? null,
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
  
   public function static(Request $request)
{ 
    $game = $request->game;

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

    $averageAge = Player::where('game', $game)->avg('age');

   $medalStats = DB::table('players')
    ->join('medal_counts', 'players.id', '=', 'medal_counts.player_id')
    ->where('players.game', $game)
    ->select(
        DB::raw('SUM(medal_counts.gold) as total_gold'),
        DB::raw('SUM(medal_counts.silver) as total_silver'),
        DB::raw('SUM(medal_counts.bronze) as total_bronze')
    )
    ->first();

    return response()->json([
        'gender_stats' => $genderStats,
        'age_stats' => $ageStats,
        'medal_stats' => $medalStats,
        'average' => $averageAge
    ]);
}

public function show($userId = null)
{
    $targetUserId = $userId ?? auth()->id();

    $player = Player::with(['user', 'socialMedia', 'medalCount'])
        ->where('user_id', $targetUserId)
        ->first();

    if (!$player) {
        return response()->json(['message' => 'Player not found'], 404);
    }

    $followersCount = Follow::where('following_id', $targetUserId)->count();

    $tournamentsCount = Tournament::where('player_id', $player->id)->count();

    $medal = $player->medalCount;

    $gold = $medal?->gold ?? 0;
    $silver = $medal?->silver ?? 0;
    $bronze = $medal?->bronze ?? 0;

    $winRate = $tournamentsCount > 0
        ? round(($gold / $tournamentsCount) * 100, 2)
        : 0;

    return response()->json([
        'id' => $player->id,
        'name' => $player->name,
        'age' => $player->age,
        'game' => $player->game,
        'contact_email' => $player->contact_email,
        'avatar_url' => $player->user->avatar 
            ? Storage::disk('s3')->url($player->user->avatar)
                : null,
        'role' => $player->user->role ?? 'Player',
        'social_media' => $player->socialMedia,
        'followersCount' => $followersCount,

        'stats' => [
            'totalTournaments' => $tournamentsCount,
            'gold' => $gold,
            'silver' => $silver,
            'bronze' => $bronze,
            'winRate' => $winRate . '%',
        ],

        'is_own_profile' => auth()->id() == $targetUserId,
    ]);
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

    if (!empty($request->contact_email)) {
        $player->contact_email = $request->contact_email;
        $player->save();
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

public function search(Request $request)
{
    $search = $request->query('search');
    $game = $request->query('game');
    $gender = $request->query('gender');
    $age = $request->query('age');
    $wins = $request->query('wins');

    $hasFilters =
        ($game && $game !== 'All Games') ||
        ($gender && $gender !== 'Gender') ||
        ($age && $age !== 'Age') ||
        ($wins && $wins !== 'Number of Wins');

    $query = Player::with(['medalCount', 'user'])
        ->withCount('tournaments');

    if ($search) {
        $query->where('name', 'LIKE', "%$search%");
    }

    if ($game && $game !== 'All Games') {
        $query->where('game', $game);
    }

    if ($gender && $gender !== 'Gender') {
        $query->where('gender', strtolower($gender));
    }

    if ($age && $age !== 'Age') {
        if ($age === '10-18') $query->whereBetween('age', [10, 18]);
        elseif ($age === '19-25') $query->whereBetween('age', [19, 25]);
        elseif ($age === '26-35') $query->whereBetween('age', [26, 35]);
        elseif ($age === '36-50') $query->whereBetween('age', [36, 50]);
        elseif ($age === '51 or more') $query->whereBetween('age', [51, 99]);
    }

    $players = $query->get()->sortByDesc(fn($p) => $p->win_rate);

    if ($wins && $wins !== 'Number of Wins') {
        $players = $players->filter(function ($player) use ($wins) {
            $winsCount = $player->wins;

            if ($wins === '80 or more') {
                return $winsCount >= 80;
            }

            [$min, $max] = explode('-', $wins);

            return $winsCount >= (int)$min && $winsCount <= (int)$max;
        });
    }

    $playersResult = $players->values()->map(function ($player, $index) {
        return [
            'id' => $player->id,
            'type' => 'player',
            'rank' => $index + 1,
            'name' => $player->name,
            'game' => $player->game,
            'winRate' => $player->win_rate . '%',
            'numberOfWins' => $player->number_of_wins,
            'avatar' => $player->user?->avatar
                ? Storage::disk('s3')->url($player->user->avatar)
                : null,
        ];
    });

    if ($search && !$hasFilters) {
        $scoutsResult = Scout::query()
            ->with('user')
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('organization_name', 'LIKE', '%' . $search . '%');
            })
            ->get()
            ->map(function ($scout) {
                return [
                    'id' => $scout->users_id,
                    'type' => 'scout',
                    'name' => $scout->name,
                    'organizationName' => $scout->organization_name,
                    'email' => $scout->workEmail,
                    'avatar' => $scout->user?->avatar
                        ? Storage::disk('s3')->url($scout->user->avatar)
                        : null,
                ];
            });

        return response()->json(
            $playersResult->concat($scoutsResult)->values()
        );
    }

    return response()->json($playersResult->values());
}
}