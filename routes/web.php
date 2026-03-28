<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;

// React app
Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

/* API
Route::get('/posts', [PostsController::class, 'index']);*/