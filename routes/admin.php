<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryAreaController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionItemController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Permission\Models\Permission;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin', 'as' => 'admin.'], function () {

        // ── Dashboard & account ───────────────────────────────────────
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('account/{id}/edit', [LoginController::class, 'editlogin'])->name('login.edit');
        Route::post('account/{id}', [LoginController::class, 'updatelogin'])->name('login.update');

        // ── Roles & employees ─────────────────────────────────────────
        Route::resource('employee', EmployeeController::class)->except(['show']);
        Route::get('role', [RoleController::class, 'index'])->name('role.index');
        Route::get('role/create', [RoleController::class, 'create'])->name('role.create');
        Route::get('role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::patch('role/{id}', [RoleController::class, 'update'])->name('role.update');
        Route::post('role', [RoleController::class, 'store'])->name('role.store');
        Route::post('role/delete', [RoleController::class, 'delete'])->name('role.delete');
        Route::delete('role/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
        Route::get('permissions/{guard_name}', fn (string $guardName) => Permission::query()->where('guard_name', $guardName)->get())
            ->name('permissions.index');

        // ── Store ─────────────────────────────────────────────────────
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('delivery-areas', DeliveryAreaController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'update', 'destroy']);

        // ── Website content ───────────────────────────────────────────
        Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
        Route::get('sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
        Route::put('sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::resource('sections.items', SectionItemController::class)->except(['index', 'show'])->scoped();

        Route::resource('branches', BranchController::class)->except(['show']);

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login', [LoginController::class, 'show_login_view'])->name('admin.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});
