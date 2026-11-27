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

/*
|--------------------------------------------------------------------------
| PBI-21: Manajemen Pesan Internal / Inbox Faskes (Briantara Pradhipta)
|--------------------------------------------------------------------------
| Sesuai Proposal Tabel 5.1 & F22:
|   C: Kirim pesan baru          (POST /api/inbox)
|   R: Lihat daftar inbox        (GET  /api/inbox)
|   R: Lihat detail pesan        (GET  /api/inbox/{id})
|   U: Tandai read/unread        (PUT  /api/inbox/{id})
|   D: Hapus pesan               (DELETE /api/inbox/{id})
|   Helper: Daftar penerima      (GET  /api/inbox/recipients)
*/
Route::middleware('auth')->group(function () {
    Route::get('/inbox', [\App\Http\Controllers\Api\InboxController::class, 'index']);
    Route::post('/inbox', [\App\Http\Controllers\Api\InboxController::class, 'store']);
    Route::get('/inbox/recipients', [\App\Http\Controllers\Api\InboxController::class, 'recipients']);
    Route::get('/inbox/{id}', [\App\Http\Controllers\Api\InboxController::class, 'show']);
    Route::put('/inbox/{id}', [\App\Http\Controllers\Api\InboxController::class, 'update']);
    Route::delete('/inbox/{id}', [\App\Http\Controllers\Api\InboxController::class, 'destroy']);
});
