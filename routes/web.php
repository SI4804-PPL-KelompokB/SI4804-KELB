<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DrugCategoryController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PenawaranController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MedStock.id
|--------------------------------------------------------------------------
| Autentikasi pengguna (Login/Logout) dan antarmuka Web untuk PBI yang telah
| diimplementasikan (PBI-01, PBI-04, PBI-07, PBI-14, PBI-21).
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

    /*
    |--------------------------------------------------------------------------
    | PBI-04: CRUD Master Kategori Obat (Khusus Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::resource('categories', DrugCategoryController::class)->except('show');
    });

    /*
    |--------------------------------------------------------------------------
    | PBI-14: CRUD Data Kendaraan / Kurir Internal (Khusus Penyedia)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:penyedia')->prefix('penyedia')->name('penyedia.')->group(function () {
        Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
        Route::get('/kendaraan/create', [KendaraanController::class, 'create'])->name('kendaraan.create');
        Route::post('/kendaraan', [KendaraanController::class, 'store'])->name('kendaraan.store');
        Route::get('/kendaraan/{kendaraan}/edit', [KendaraanController::class, 'edit'])->name('kendaraan.edit');
        Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'update'])->name('kendaraan.update');
        Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroy'])->name('kendaraan.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PBI-01: Manajemen Data Wilayah
    |--------------------------------------------------------------------------
    | READ  â†’ semua role yang terautentikasi
    | WRITE â†’ hanya admin dan dinkes
    */
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');

    Route::middleware('role:admin,dinkes')->group(function () {
        Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
        Route::put('/wilayah/{id}', [WilayahController::class, 'update'])->name('wilayah.update');
        Route::delete('/wilayah/{id}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PBI-07: Pencatatan Permintaan Darurat Faskes (Antarmuka Web)
    |--------------------------------------------------------------------------
    | Selaras dengan otorisasi API: faskes, dinkes, dan admin dapat mengakses
    | halaman; penyedia ditolak (403). Operasi tulis dilakukan via /api/permintaan.
    */
    Route::get('/permintaan', [PermintaanController::class, 'index'])
        ->middleware('role:faskes,dinkes,admin')
        ->name('permintaan.index');

    /*
    |--------------------------------------------------------------------------
    | PBI-09: Pencatatan Dokumen Penawaran Surplus (Antarmuka Web)
    |--------------------------------------------------------------------------
    */
    Route::get('/penawaran', [PenawaranController::class, 'index'])
        ->middleware('role:faskes,dinkes,admin')
        ->name('penawaran.index');

    /*
    |--------------------------------------------------------------------------
    | PBI-21: Manajemen Pesan Internal / Inbox Faskes (Briantara Pradhipta)
    |--------------------------------------------------------------------------
    | Semua pengguna terautentikasi dapat membaca dan mengirim pesan internal.
    */
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::post('/inbox', [InboxController::class, 'store'])->name('inbox.store');
    Route::put('/inbox/{id}', [InboxController::class, 'update'])->name('inbox.update');
    Route::delete('/inbox/{id}', [InboxController::class, 'destroy'])->name('inbox.destroy');
});
