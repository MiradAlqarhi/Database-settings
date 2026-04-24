
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIimageController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class,'login']);

Route::post('/AI', [AIimageController::class,'analyze']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
  
   // Route::post('/logout', [AuthController::class, 'logout']);
    
   // Route::put('/user/profile', [AuthController::class, 'updateProfile']);

   // Route::put('/user/password', [AuthController::class, 'changePassword']);
});