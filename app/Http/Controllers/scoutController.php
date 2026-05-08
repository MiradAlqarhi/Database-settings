<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;              
use Illuminate\Support\Facades\Hash;     
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\otp;
use App\Models\scout;

 class scoutController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $otp = rand(0000, 9999);

        Otp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(1),
            ]
        );

        Mail::raw("Your OTP code is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('OTP Verification');
        });

        return response()->json([
            'message' => 'OTP sent successfully'
        ]);
    }
    
   public function verifyOtp(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'organization_name' => 'required|string',
        'workEmail' => 'required|email',
        'OTP' => 'required|string',
    ]);

    $scoutOtp = Otp::where('email', $request->workEmail)
                    ->latest()
                    ->first();

    if (!$scoutOtp) {
        return response()->json([
            'message' => 'Work email not found'
        ], 404);
    }

    if (Carbon::now()->gt(Carbon::parse($scoutOtp->expires_at))) {
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

    return $this->store($request);
}

public function store(Request $request)
{
    $scout = scout::create([
        'name' => $request->name,
        'organization_name' => $request->organization_name,
        'workEmail' => $request->workEmail,
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
}
