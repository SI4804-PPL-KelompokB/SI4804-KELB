<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MedStock.id
|--------------------------------------------------------------------------
| Sesuai instruksi pengguna: Hanya mengimplementasikan fitur Login dan Logout
| berdasarkan Kebutuhan F1, NF1, Class Diagram User, dan Mockup 4.5.
*/

// Redirect root ke dashboard jika login, atau ke formulir login jika belum
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rute Autentikasi Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1') // Rate limiting keamanan proteksi brute force
        ->name('login.attempt');
});

// Rute Terotentikasi (Auth)
Route::middleware('auth')->group(function () {
    // Aksi Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Central Dashboard Redirector
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard masing-masing Peran dengan Proteksi Otorisasi RoleMiddleware
    Route::get('/faskes/dashboard', [DashboardController::class, 'faskes'])
        ->middleware('role:faskes')
        ->name('faskes.dashboard');

    Route::get('/dinkes/dashboard', [DashboardController::class, 'dinkes'])
        ->middleware('role:dinkes')
        ->name('dinkes.dashboard');

    Route::get('/penyedia/dashboard', [DashboardController::class, 'penyedia'])
        ->middleware('role:penyedia')
        ->name('penyedia.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('admin.dashboard');
});
