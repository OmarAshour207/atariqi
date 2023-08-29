<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'    => 'locale'
], function () {

    Route::get('/get/config', [\App\Http\Controllers\Api\HomeController::class, 'get']);

    Route::controller(\App\Http\Controllers\Api\UserController::class)->group(function() {
        Route::post('user/register', 'register');
        Route::post('user/login', 'login');
        Route::post('user/verify', 'verifyCode');
    });

    Route::middleware(['auth:sanctum'])->group(function () {

    });
});

