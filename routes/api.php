<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear/cache', function () {
    return \Illuminate\Support\Facades\Artisan::call('config:cache');
});

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
        Route::post('immediate/transport/change/status', [\App\Http\Controllers\Api\ImmediateDriverController::class, 'changeStatus']);
        Route::post('drivers/rate', [\App\Http\Controllers\Api\ImmediateDriverController::class, 'rate']);

        Route::post('drivers/daily/transport', [\App\Http\Controllers\Api\DailyDriverController::class, 'getDrivers']);
        Route::post('drivers/daily/select', [\App\Http\Controllers\Api\DailyDriverController::class, 'selectDriver']);
        Route::post('drivers/daily/send/all', [\App\Http\Controllers\Api\DailyDriverController::class, 'sendToAllDrivers']);

        Route::post('daily/transport/get/notifications', [\App\Http\Controllers\Api\DailyDriverController::class, 'getUserNotification']);
        Route::post('daily/transport/get/summary', [\App\Http\Controllers\Api\DailyDriverController::class, 'getUserSummary']);

        Route::post('daily/transport/trip', [\App\Http\Controllers\Api\DailyDriverController::class, 'getTripDetails']);

        Route::post('daily/transport/execute', [\App\Http\Controllers\Api\DailyDriverController::class, 'executeRide']);
        Route::post('daily/transport/change/action', [\App\Http\Controllers\Api\DailyDriverController::class, 'changeAction']);

    });
});

