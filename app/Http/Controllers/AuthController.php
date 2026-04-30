<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        ]);

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user),
            'token' => $token,
            'message' => 'registered and logged in'
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login'], 401);
        }

        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'user' => $this->formatUser($user),
            'token' => $token,
            'profile_completed' => $user->profile_completed
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $this->formatUser($request->user()),
        ]);
    }

    private function formatUser($user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'type' => $user->type,
            'profile_completed' => $user->profile_completed,
        ];
    }
}