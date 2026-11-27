<?php

use App\Http\Controllers\Api\PermintaanController;
use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — MedStock.id
|--------------------------------------------------------------------------
| Seluruh endpoint di bawah menggunakan session-based authentication (guard web)
| yang telah diaktifkan pada group 'api' di bootstrap/app.php.
| Request yang mengubah state (POST, PUT, DELETE) dari browser wajib menyertakan
| header X-XSRF-TOKEN (CSRF token dari cookie XSRF-TOKEN).
*/

Route::middleware('auth')->group(function () {
    // =========================================================================
    // PBI-01: Manajemen Data Wilayah / Zona Faskes
    // =========================================================================
    // Read: semua role yang terautentikasi (admin, dinkes, faskes, penyedia)
    Route::get('/wilayah', [WilayahController::class, 'index']);
    Route::get('/wilayah/{id}', [WilayahController::class, 'show']);

    // Write: hanya admin dan dinkes
    Route::middleware('role:admin,dinkes')->group(function () {
        Route::post('/wilayah', [WilayahController::class, 'store']);
        Route::put('/wilayah/{id}', [WilayahController::class, 'update']);
        Route::delete('/wilayah/{id}', [WilayahController::class, 'destroy']);
    });

    // =========================================================================
    // PBI-07: Pencatatan Permintaan Darurat Faskes
    // =========================================================================
    // Read: faskes (milik sendiri), dinkes & admin (lintas fasilitas); penyedia ditolak (403)
    Route::middleware('role:faskes,dinkes,admin')->group(function () {
        Route::get('/permintaan', [PermintaanController::class, 'index']);
        Route::get('/permintaan/{id}', [PermintaanController::class, 'show']);
    });

    // Write: hanya role faskes (membuat, mengubah urgensi, dan menghapus permintaan milik sendiri)
    Route::middleware('role:faskes')->group(function () {
        Route::post('/permintaan', [PermintaanController::class, 'store']);
        Route::put('/permintaan/{id}', [PermintaanController::class, 'update']);
        Route::delete('/permintaan/{id}', [PermintaanController::class, 'destroy']);
    });
});
