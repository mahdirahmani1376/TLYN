<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => UserController::class,
    'prefix' => 'users',
], function () {
    Route::get('/', 'show')->name('users.show')->middleware('auth:sanctum');
    Route::post('/login', 'login')->name('users.login');
    Route::post('/register', 'register')->name('users.register');
    Route::put('/update', 'update')->name('users.update')->middleware('auth:sanctum');
});

Route::group([
    'controller' => OrderController::class,
    'prefix' => '/orders',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/', 'index')->name('orders.index');
    Route::get('{orderId}', 'show')->name('orders.show');
    Route::post('/buy', 'buy')->name('orders.buy');
    Route::post('/sell', 'sell')->name('orders.sell');
    Route::post('/{orderId}', 'cancel')->name('orders.cancel');
});
