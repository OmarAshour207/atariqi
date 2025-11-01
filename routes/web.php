<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Driver\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('payment/telr')->group(function () {
    Route::get('success', [PaymentController::class, 'success'])->name('telr.payment.success');
    Route::get('failed', [PaymentController::class, 'failed'])->name('telr.payment.failed');
    Route::get('declined', [PaymentController::class, 'declined'])->name('telr.payment.declined');
});
