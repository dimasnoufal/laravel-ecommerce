<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\RegionController;

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

        Route::get('admin/master_data/{path?}', function ($path = '') {
            return redirect('/admin/master-data/' . $path);
        })->where('path', '.*');
    });
});
