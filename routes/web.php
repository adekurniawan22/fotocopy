<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OptionController;

// 1. Halaman Depan (Daftar Item)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Routes Authentication (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 3. Route Logout (Auth Only)
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/logout-katalog', [AuthController::class, 'logoutKatalog'])->middleware('auth')->name('logout.katalog');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Users
    Route::resource('users', UserController::class);

    // Items
    Route::resource('items', ItemController::class);

    // Options
    // Route::resource('options', OptionController::class);
    Route::get('/options', function () {
        return view('auth.coming-soon');
    })->name('options.index');
});
