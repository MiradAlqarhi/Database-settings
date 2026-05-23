<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\Scout;

class ScoutController extends Controller
{

public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $email = strtolower(trim($request->email));

    if (Scout::whereRaw('LOWER(workEmail) = ?', [$email])->exists()) {
        return response()->json([
            'message' => 'This email is not allowed'
        ], 403);
    }

    $key = 'otp:' . $email;

    if (RateLimiter::tooManyAttempts($key, 1)) {
        return response()->json([
            'message' => 'Please wait 2 minutes before requesting again'
        ], 429);
    }

    RateLimiter::hit($key, 120);

    $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    Otp::updateOrCreate(
        ['email' => $email],
        [
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(2),
        ]
    );

    Mail::raw("Your OTP code is: $otp", function ($message) use ($email) {
        $message->to($email)
                ->subject('OTP Verification');
    });

    return response()->json([
        'message' => 'OTP sent successfully'
    ]);
}

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
            'workEmail' => 'required|email',
            'OTP' => 'required|string|size:4',
        ]);

        $email = strtolower(trim($request->workEmail));

        $scoutOtp = Otp::where('email', $email)
            ->latest()
            ->first();

        if (!$scoutOtp) {
            return response()->json([
                'message' => 'Work email not found'
            ], 404);
        }

        if (Carbon::now()->gt(Carbon::parse($scoutOtp->expires_at))) {
            $scoutOtp->delete();

            return response()->json([
                'message' => 'OTP has expired'
            ], 400);
        }

        if (!Hash::check($request->OTP, $scoutOtp->otp)) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        $scoutOtp->delete();

        return $this->store($request, $email);
    }

    public function store(Request $request, $email)
    {
        $scout = Scout::create([
            'name' => $request->name,
            'organization_name' => $request->organization_name,
            'workEmail' => $email,
            'users_id' => auth()->id(),
        ]);

        $user = auth()->user();

        if ($user) {
            $user->profile_completed = true;
            $user->save();
        }

        return response()->json([
            'message' => 'Success! Scout saved.',
            'scout' => $scout
        ], 201);
    }

public function show($id)
{
    $scout = Scout::with(['user', 'socialMedia'])
        ->where('users_id', $id)
        ->first();

    \Log::info('Scout query result: ' . ($scout ? 'found id='.$scout->id : 'null'));

    if (!$scout) {
        return response()->json(['message' => 'Scout not found'], 404);
    }

    return response()->json([
        'id' => $scout->users_id,
        'name' => $scout->name,
        'organizationName' => $scout->organization_name,
        'social_media' => $scout->socialMedia,
        'avatar_url' => $scout->user?->avatar
            ? Storage::disk('s3')->url($scout->user->avatar)
            : null,
        'workEmail' => $scout->workEmail,
        'type' => 'scout',
        'is_own_profile' => auth()->id() == $scout->users_id,
    ]);
}
}