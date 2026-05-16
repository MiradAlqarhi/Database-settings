<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Tournament; 
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        return response()->json(Notification::all(), 200);
    }

    public function checkTournamentCalendar()
    {
        $todayTournaments = Tournament::whereDate('start_date', Carbon::today())->get();

        foreach ($todayTournaments as $tournament) {
            Notification::create([
                'title' => 'Tournament Today!',
                'message' => 'The tournament ' . $tournament->name . ' starts today.',
                'type' => 'tournament',
                'tournament_id' => $tournament->id,
                'is_read' => false
            ]);
        }

        $upcomingTournaments = Tournament::whereDate('start_date', Carbon::tomorrow())->get();

        foreach ($upcomingTournaments as $tournament) {
            Notification::create([
                'title' => 'Upcoming Tournament!',
                'message' => 'The tournament ' . $tournament->name . ' is starting tomorrow.',
                'type' => 'tournament',
                'tournament_id' => $tournament->id,
                'is_read' => false
            ]);
        }

        return response()->json(['message' => 'Tournament notifications checked successfully.'], 200);
    }

    public function sendFollowNotification(Request $request)
    {
        $request->validate([
            'related_user_id' => 'required|integer'
        ]);

        Notification::create([
            'title' => 'New Follower!',
            'message' => 'Someone started following your profile.',
            'type' => 'follow',
            'related_user_id' => $request->related_user_id,
            'is_read' => false
        ]);

        return response()->json(['message' => 'Follow notification sent successfully.'], 201);
    }
}