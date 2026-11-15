<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('orders', [OrderController::class, 'store'])->middleware('throttle:orders')->name('orders.store');

    // ── Customer account (JSON endpoints used by the storefront script) ──
    Route::prefix('account')->name('account.')->group(function () {
        Route::middleware(['guest:customer', 'throttle:customer-auth'])->group(function () {
            Route::post('register', [AuthController::class, 'register'])->name('register');
            Route::post('login', [AuthController::class, 'login'])->name('login');
        });

        Route::middleware('auth:customer')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', [AccountController::class, 'show'])->name('show');
            Route::put('profile', [AccountController::class, 'updateProfile'])->name('profile');
            Route::put('favourites', [AccountController::class, 'syncFavourites'])->name('favourites');
            Route::post('addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
            Route::put('addresses/{address}', [AccountController::class, 'updateAddress'])->whereNumber('address')->name('addresses.update');
            Route::delete('addresses/{address}', [AccountController::class, 'destroyAddress'])->whereNumber('address')->name('addresses.destroy');
        });
    });
});
