
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIimageController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class,'login']);

Route::post('/AI', [AIimageController::class,'analyze']);

Route::middleware('auth:sanctum')->get('/user', function () {
    return auth()->user();
});