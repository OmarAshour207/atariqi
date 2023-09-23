<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'    => 'locale'
], function () {

    Route::get('/get/config', [\App\Http\Controllers\Api\HomeController::class, 'get']);

    Route::controller(\App\Http\Controllers\Api\UserController::class)->group(function() {
        Route::post('user/register', 'register');
        Route::post('user/send', 'send');
        Route::post('user/verify', 'verifyCode');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('drivers/immediate/transport', [\App\Http\Controllers\Api\ImmediateDriverController::class, 'getDrivers']);
        Route::post('immediate/transport/trips', [\App\Http\Controllers\Api\ImmediateDriverController::class, 'get']);
        Route::post('drivers/immediate/transport/accept', [\App\Http\Controllers\Api\ImmediateDriverController::class, 'accept']);
        Route::post('drivers/rate', [\App\Http\Controllers\Api\ImmediateDriverController::class, 'rate']);
    });
});

