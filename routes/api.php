<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function () {
    return request()->user();
});

use App\Http\Controllers\UserController;

Route::post('/register', [UserController::class, 'register'])->name('register.post');
