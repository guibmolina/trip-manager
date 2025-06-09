<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\OrderController;

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('jwt')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'getUser']);
    Route::post('/orders', [OrderController::class, 'create']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::patch('/orders/{id}', [OrderController::class, 'updateStatus']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders', [OrderController::class, 'index']);
});
Route::get('/destinations', [DestinationController::class, 'index']);
