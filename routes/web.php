<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Driver\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Dashboard\HomeController as DashboardHomeController;
use App\Http\Controllers\Dashboard\Auth\LoginController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\HomepageSectionController;
use App\Http\Controllers\Dashboard\HomepageStatController;
use App\Http\Controllers\Dashboard\TestimonialController;
use App\Http\Controllers\Dashboard\PartnerAchievementController;
use App\Http\Controllers\Dashboard\EditDriverInfoRequestController;
use App\Http\Controllers\Dashboard\PackageController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\DriverController;

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

Route::get('/', [HomeController::class, 'home'])->name('home')->middleware('locale');
Route::get('/support', [HomeController::class, 'support'])->name('support');
Route::get('/homepage-sections', [HomeController::class, 'homepageSections'])->name('homepage.sections');
Route::get('/locale/{locale}', [HomeController::class, 'changeLocale'])->name('change.locale');

Route::get('dashboard/login', [LoginController::class, 'showLogin'])->name('dashboard.loginForm');
Route::post('dashboard/login', [LoginController::class, 'login'])->name('dashboard.login');

Route::middleware(['is_admin'])->prefix('dashboard')->group(function () {
    Route::get('index', [DashboardHomeController::class, 'index'])->name('dashboard.index');

    Route::Resource('homepage-sections', HomepageSectionController::class);

    Route::Resource('homepage-stats', HomepageStatController::class);

    Route::Resource('testimonials', TestimonialController::class);

    Route::Resource('partner-achievements', PartnerAchievementController::class);

    Route::resource('packages', PackageController::class);

    Route::get('driver/packages', [DriverController::class, 'packages'])->name('drivers.packages');
    Route::get('drivers/{driver}/packages', [DriverController::class, 'packagePlans'])->name('drivers.packagePlans');
    Route::post('drivers/{driver}/packages/assign', [DriverController::class, 'assignPackage'])->name('drivers.assignPackage');
    Route::get('driver/rates', [DriverController::class, 'driverRates'])->name('drivers.rates');
    Route::get('driver/trips', [DriverController::class, 'trips'])->name('drivers.trips');
    Route::post('drivers/{driver}/send-payment-reminder', [DriverController::class, 'sendPaymentReminder'])->name('drivers.sendPaymentReminder');
    Route::post('drivers/{driver}/update-status', [DriverController::class, 'updateStatus'])->name('drivers.updateStatus');

    Route::Resource('drivers', DriverController::class);

    Route::get('new-drivers', [DriverController::class, 'newDrivers'])->name('new-drivers.index');

    Route::get('user/trips', [UserController::class, 'index'])->name('users.trips');
    Route::get('user/rates', [UserController::class, 'rates'])->name('users.rates');
    Route::get('user/unride-rates', [UserController::class, 'unrideRates'])->name('users.unride-rates');

    Route::Resource('users', UserController::class);

    Route::Resource('edit-info-request', EditDriverInfoRequestController::class);

    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('logout', [LoginController::class, 'logout'])->name('dashboard.logout');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/store', [SettingController::class, 'store'])->name('settings.store');

    Route::get('/language/{locale}', [SettingController::class, 'changeLocale'])->name('language');
});

Route::prefix('payment/telr')->group(function () {
    Route::get('success', [PaymentController::class, 'success'])->name('telr.payment.success');
    Route::get('failed', [PaymentController::class, 'failed'])->name('telr.payment.failed');
    Route::get('declined', [PaymentController::class, 'declined'])->name('telr.payment.declined');
});
