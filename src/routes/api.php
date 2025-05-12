<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => UserController::class,
    'prefix' => 'users',
], function () {
    Route::get('/', 'index')->name('users.index')->middleware('auth:sanctum');
    Route::get('/{id}', 'show')->name('users.show')->middleware('auth:sanctum');
    Route::post('/login', 'login')->name('users.login');
    Route::post('/register', 'register')->name('users.register');
    Route::put('/update', 'update')->name('users.update')->middleware('auth:sanctum');
});

Route::group([
    'controller' => OrderController::class,
    'prefix' => 'orders',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', 'index');
    Route::post('/buy', 'buy');
    Route::post('/sell', 'sell');
    Route::delete('/{order}', 'destroy');
});

Route::get('transactions', [TransactionController::class, 'index'])->middleware('auth:sanctum');
