<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;

// React app
Route::get('/', function () {
    return view('app');
});

Route::get('{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');

