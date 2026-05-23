<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIimageController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\scoutController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\s3Controller;
use Illuminate\Http\Request;


  Route::post('/register', [AuthController::class, 'register']);

  Route::post('/login', [AuthController::class,'login']);

  Route::get('/player', [PlayerController::class, 'static']);

  Route::post('/otp', [scoutController::class, 'sendOtp']);

  Route::post('/scouts', [AuthController::class, 'store']);

  Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'user']);
  
    Route::post('/AI', [AIimageController::class,'analyze']);

    Route::post('/player-profile', [PlayerController::class, 'store']);

    //Route::get('/tournaments', [PlayerController::class, 'saveTournaments']);

    Route::post('/verify-otp', [scoutController::class, 'verifyOtp']);

     Route::get('/search', [PlayerController::class, 'search']);

   Route::get('/player-profile/{id}', [PlayerController::class, 'show']);//اضفت GETعشان ياخذ البيانات 

   Route::get('/scouts/{id}', [scoutController::class, 'show']);

   Route::post('/update-profile', [PlayerController::class, 'updateProfile']);//اضفت ذا

    Route::post('/tournaments', [AIimageController::class, 'saveTournaments']);//عدلت ذا

    Route::get('/gettournaments', [AIimageController::class, 'getTournaments']);//اضفت ذا

    Route::post('/follow/{id}', [FollowController::class, 'follow']);

    Route::delete('/follow/{id}', [FollowController::class, 'unfollow']);

    Route::get('/follow/{id}/status', [FollowController::class, 'isFollowing']);

    Route::get('/following', [FollowController::class, 'following']);

     Route::post('/videos',[s3Controller::class, 'presign']);

     Route::post('/storevideos',[s3Controller::class, 'store']);

     Route::post('/avatar',[s3Controller::class,  'updateAvatar']);

   // Route::post('/logout', [AuthController::class, 'logout']);
    
   // Route::put('/user/profile', [AuthController::class, 'updateProfile']);

   // Route::put('/user/password', [AuthController::class, 'changePassword']);
});
