<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
   public function register(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'type' => 'required|in:player,scout',
    ]);

    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'type' => $request->type,
        'profile_completed' => false,
    ]);

    Auth::login($user);// make the user logged in after registration

    return response()->json([
        'user' => $user,
        'message' => 'registered and logged in'
    ]);
}

  public function login(Request $request)
{
     $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid login'], 401);
    }

    $user = Auth::user();

    return response()->json([
        'user' => $user,
        'profile_completed' => $user->profile_completed
    ]);
}
}