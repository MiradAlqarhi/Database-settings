
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIimageController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\scoutController;
use Illuminate\Http\Request;


  Route::post('/register', [AuthController::class, 'register']);

  Route::post('/login', [AuthController::class,'login']);

  Route::get('/player', [PlayerController::class, 'index']);

  Route::post('/otp', [scoutController::class, 'sendOtp']);


 // Route::post('/scouts', [AuthController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
  
    Route::post('/AI', [AIimageController::class,'analyze']);

    Route::post('/player-profile', [PlayerController::class, 'store']);

    Route::get('/tournaments', [PlayerController::class, 'saveTournaments']);

    Route::post('/verify-otp', [scoutController::class, 'verifyOtp']);
   // Route::post('/logout', [AuthController::class, 'logout']);
    
   // Route::put('/user/profile', [AuthController::class, 'updateProfile']);

   // Route::put('/user/password', [AuthController::class, 'changePassword']);
});