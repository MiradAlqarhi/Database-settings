
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIimageController;
use App\Http\Controllers\PlayerController;


  Route::post('/register', [AuthController::class, 'register']);

  Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
  
    Route::post('/AI', [AIimageController::class,'analyze']);

    Route::post('/player-profile', [PlayerController::class, 'store']);
   // Route::post('/logout', [AuthController::class, 'logout']);
    
   // Route::put('/user/profile', [AuthController::class, 'updateProfile']);

   // Route::put('/user/password', [AuthController::class, 'changePassword']);
});