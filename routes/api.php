<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    ImmediateDriverController,
    DailyDriverController,
    WeeklyDriverController,
    UserController,
    HomeController
};

use App\Http\Controllers\Api\Driver\{
    RegisterController,
    LoginController,
    ProfileController,
    ServiceController,
    DriverController,
    SummaryController,
    TripController,
    DailyTripsController,
    WeeklyTripController
};

Route::group([
    'middleware'    => 'locale'
], function () {

    Route::get('/get/config', [HomeController::class, 'get']);
    Route::get('get/announce', [HomeController::class, 'getAnnouncement']);
    Route::get('get/contacts', [HomeController::class, 'getContacts']);

    // passenger
    Route::controller(UserController::class)->group(function() {
        Route::post('user/register', 'register');
        Route::post('user/send', 'send');
        Route::post('user/verify', 'verifyCode');
    });

    // Driver
    Route::post('driver/register', [RegisterController::class, 'register']);
    Route::post('driver/login', [LoginController::class, 'login']);
    Route::post('driver/verify', [LoginController::class, 'verify']);

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::middleware('is_passenger')->group(function() {
            // Immediate
            Route::post('drivers/immediate/transport', [ImmediateDriverController::class, 'getDrivers']);
            Route::post('immediate/transport/trips', [ImmediateDriverController::class, 'get']);
            Route::post('immediate/transport/change/action', [ImmediateDriverController::class, 'changeAction']);
            Route::post('immediate/transport/execute', [ImmediateDriverController::class, 'execute']);

            Route::post('drivers/rate', [ImmediateDriverController::class, 'rate']);

            // Daily
            Route::post('drivers/daily/transport', [DailyDriverController::class, 'getDrivers']);
            Route::post('drivers/daily/select', [DailyDriverController::class, 'selectDriver']);
            Route::post('drivers/daily/send/all', [DailyDriverController::class, 'sendToAllDrivers']);

            Route::post('daily/transport/get/notifications', [DailyDriverController::class, 'getUserNotification']);
            Route::post('daily/transport/get/summary', [DailyDriverController::class, 'getUserSummary']);

            Route::post('daily/transport/trip', [DailyDriverController::class, 'getTripDetails']);

            Route::post('daily/transport/execute', [DailyDriverController::class, 'executeRide']);
            Route::post('daily/transport/change/action', [DailyDriverController::class, 'changeAction']);

            // Weekly
            Route::post('drivers/weekly/transport', [WeeklyDriverController::class, 'getDrivers']);
            Route::post('drivers/weekly/select', [WeeklyDriverController::class, 'selectDriver']);
            Route::post('drivers/weekly/send/all', [WeeklyDriverController::class, 'sendToAllDrivers']);

            Route::post('weekly/transport/get/notifications', [WeeklyDriverController::class, 'getUserNotification']);
            Route::post('weekly/transport/get/summary', [WeeklyDriverController::class, 'getUserSummary']);

            Route::post('weekly/transport/trip', [WeeklyDriverController::class, 'getTripDetails']);

            Route::post('weekly/transport/execute', [WeeklyDriverController::class, 'executeRide']);
            Route::post('weekly/transport/change/action', [WeeklyDriverController::class, 'changeAction']);

            // User
            Route::post('profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
        });

        // Driver
        Route::middleware('is_driver')->prefix('driver')->group(function() {
            Route::post('general/update', [ProfileController::class, 'updateGeneral']);
            Route::post('info/update', [ProfileController::class, 'updateInfo']);
            Route::post('car/update', [ProfileController::class, 'updateCar']);
            Route::post('transport/update', [ProfileController::class, 'updateTransport']);
            Route::post('transport/index', [ProfileController::class, 'getTransportData']);

            Route::post('service/start', [ServiceController::class, 'start']);
            Route::post('service/stop', [ServiceController::class, 'stop']);

            Route::post('rate', [DriverController::class, 'driverRate']);

            Route::post('summary', [SummaryController::class, 'summaryAll']);

            Route::get('summary/search', [SummaryController::class, 'summary']);

            Route::post('trip/action/update', [TripController::class, 'updateAction']);

            Route::get('trip/{type}/{id}', [TripController::class, 'get']);

            Route::post('trip/start', [TripController::class, 'start']);

            Route::post('trip/delivery/update', [TripController::class, 'updateDelivery']);

            Route::post('trip/rate', [TripController::class, 'rate']);

            Route::get('trips/daily', [DailyTripsController::class, 'get']);

            Route::post('trips/daily/accept', [DailyTripsController::class, 'accept']);

            Route::get('weekly/{group_id}', [WeeklyTripController::class, 'get']);

            Route::post('weekly/action/update', [WeeklyTripController::class, 'updateAction']);
        });
    });
});

