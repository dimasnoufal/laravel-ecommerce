<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Protected routes for admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Master Data
        Route::prefix('admin/master-data')->name('admin.')->group(function () {
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('brands', BrandController::class)->except(['create', 'show']);
            Route::resource('categories', CategoryController::class)->except(['create', 'show']);
            Route::resource('attributes', AttributeController::class)->except(['create', 'show']);
            Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
            Route::get('regions', [RegionController::class, 'index'])->name('regions.index');
        });

        // Inventory & Stock Movements
        Route::prefix('admin/inventory')->name('admin.inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::post('/adjust', [InventoryController::class, 'adjustStock'])->name('adjust');
            Route::get('/variant/{variant}', [InventoryController::class, 'getVariant'])->name('variant');
        });

        // System & Activity Logs
        Route::prefix('admin/system')->name('admin.')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        // User & Access Control Management
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::resource('users', UserController::class)->except(['create', 'show']);
            Route::resource('roles', RoleController::class)->except(['create', 'show']);
        });

        Route::get('admin/master_data/{path?}', function ($path = '') {
            return redirect('/admin/master-data/' . $path);
        })->where('path', '.*');
    });
});
