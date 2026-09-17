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
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\OrderController as StorefrontOrderController;
use App\Http\Controllers\Storefront\RegionApiController;

// ==========================================
// STOREFRONT PUBLIC ROUTES (Guests & Users)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products/{slug}', [StorefrontProductController::class, 'show'])->name('products.show');
Route::get('/api/products/{id}/variant', [StorefrontProductController::class, 'getVariant'])->name('api.products.variant');

// Cart Operations (Supports Guest Session & Authenticated User)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
});

// Cascading Region APIs (Kemendagri Data)
Route::prefix('api/regions')->name('api.regions.')->group(function () {
    Route::get('provinces', [RegionApiController::class, 'provinces'])->name('provinces');
    Route::get('provinces/{province}/regencies', [RegionApiController::class, 'regencies'])->name('regencies');
    Route::get('regencies/{regency}/districts', [RegionApiController::class, 'districts'])->name('districts');
    Route::get('districts/{district}/villages', [RegionApiController::class, 'villages'])->name('villages');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // AJAX Auth Modal Endpoints
    Route::post('/ajax/login', [AuthController::class, 'ajaxLogin'])->name('ajax.login');
    Route::post('/ajax/register', [AuthController::class, 'ajaxRegister'])->name('ajax.register');
});

// Authenticated Customer & Admin Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Checkout Process
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    });

    // Customer Orders & Tracking
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [StorefrontOrderController::class, 'index'])->name('index');
        Route::get('/{order_number}', [StorefrontOrderController::class, 'show'])->name('show');
        Route::post('/{order_number}/simulate-pay', [StorefrontOrderController::class, 'simulatePayment'])->name('simulate-pay');
    });
    
    // Protected routes for admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Orders Management
        Route::prefix('admin/orders')->name('admin.orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        });
        
        // Master Data
        Route::prefix('admin/master-data')->name('admin.')->group(function () {
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('brands', BrandController::class)->except(['create', 'show']);
            Route::resource('categories', CategoryController::class)->except(['create', 'show']);
            Route::resource('attributes', AttributeController::class)->except(['create', 'show']);
            Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
            Route::get('regions', [RegionController::class, 'index'])->name('regions.index');

            // Shipping & Logistics Master Data
            Route::get('shipping', [ShippingController::class, 'index'])->name('shipping.index');
            Route::post('shipping/carriers', [ShippingController::class, 'storeCarrier'])->name('shipping.carriers.store');
            Route::get('shipping/carriers/{carrier}/edit', [ShippingController::class, 'editCarrier'])->name('shipping.carriers.edit');
            Route::put('shipping/carriers/{carrier}', [ShippingController::class, 'updateCarrier'])->name('shipping.carriers.update');
            Route::patch('shipping/carriers/{carrier}/toggle-status', [ShippingController::class, 'toggleCarrierStatus'])->name('shipping.carriers.toggle-status');
            Route::delete('shipping/carriers/{carrier}', [ShippingController::class, 'destroyCarrier'])->name('shipping.carriers.destroy');
            Route::get('shipping/carriers/{carrier}/services', [ShippingController::class, 'getCarrierServices'])->name('shipping.carriers.services');
            Route::post('shipping/services', [ShippingController::class, 'storeService'])->name('shipping.services.store');
            Route::get('shipping/services/{service}/edit', [ShippingController::class, 'editService'])->name('shipping.services.edit');
            Route::put('shipping/services/{service}', [ShippingController::class, 'updateService'])->name('shipping.services.update');
            Route::patch('shipping/services/{service}/toggle-status', [ShippingController::class, 'toggleServiceStatus'])->name('shipping.services.toggle-status');
            Route::delete('shipping/services/{service}', [ShippingController::class, 'destroyService'])->name('shipping.services.destroy');
        });

        // Inventory & Stock Movements
        Route::prefix('admin/inventory')->name('admin.inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::post('/adjust', [InventoryController::class, 'adjustStock'])->name('adjust');
            Route::get('/variant/{variant}', [InventoryController::class, 'getVariant'])->name('variant');
        });

        // Analytics & Business Intelligence
        Route::prefix('admin/analytics')->name('admin.analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/export-live', [AnalyticsController::class, 'exportLive'])->name('export-live');
            Route::get('/print-live', [AnalyticsController::class, 'printLive'])->name('print-live');
        });

        // Report Document Archives & Generation
        Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/{report}/download', [ReportController::class, 'download'])->name('download');
            Route::get('/{report}/print', [ReportController::class, 'printReport'])->name('print');
            Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
        });

        // System, Activity Logs & Settings
        Route::prefix('admin/system')->name('admin.')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
            Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
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
