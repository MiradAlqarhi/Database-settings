<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'type' => 'required',
        'profile_completed' => 'boolean',
    ], [
        'email.unique' => 'The email has already exists.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first()
        ], 422);
    }
    

    $user = User::create([
        'email' => $request->email,
        'password' => $request->password,
        'type' => $request->type,
        'profile_completed' => false,
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return response()->json([
        'user' => $this->formatUser($user),
        'message' => 'registered and logged in'
    ]);
}


public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::guard('web')->attempt($credentials)) {
        return response()->json(['message' => 'Invalid login'], 401);
    }
    $request->session()->regenerate();

    return response()->json([
       'user'=> $this->formatUser(Auth::user()),
        'profile_completed' => Auth::user()->profile_completed
    ]);
}


 public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $this->formatUser($request->user()),
        ]);
    }
}