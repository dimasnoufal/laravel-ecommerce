<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Example protected route for admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return 'Admin Dashboard';
        })->name('admin.dashboard');
    });
});
