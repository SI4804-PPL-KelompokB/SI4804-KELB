<?php

use App\Http\Controllers\Api\InboxController;
use App\Http\Controllers\Api\PermintaanController;
use App\Http\Controllers\Api\PermintaanItemController;
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
    Route::get('/wilayah', [WilayahController::class, 'index']);
    Route::get('/wilayah/{id}', [WilayahController::class, 'show']);

    Route::middleware('role:admin,dinkes')->group(function () {
        Route::post('/wilayah', [WilayahController::class, 'store']);
        Route::put('/wilayah/{id}', [WilayahController::class, 'update']);
        Route::delete('/wilayah/{id}', [WilayahController::class, 'destroy']);
    });

    // =========================================================================
    // PBI-07 & PBI-08: Permintaan Darurat Faskes & Detail Item Obat
    // =========================================================================
    Route::middleware('role:faskes,dinkes,admin')->group(function () {
        Route::get('/permintaan', [PermintaanController::class, 'index']);
        Route::get('/permintaan/{id}', [PermintaanController::class, 'show']);
        Route::get('/permintaan/{id}/items', [PermintaanItemController::class, 'index']);
    });

    Route::middleware('role:faskes')->group(function () {
        Route::post('/permintaan', [PermintaanController::class, 'store']);
        Route::put('/permintaan/{id}', [PermintaanController::class, 'update']);
        Route::delete('/permintaan/{id}', [PermintaanController::class, 'destroy']);

        Route::post('/permintaan/{id}/items', [PermintaanItemController::class, 'store']);
        Route::put('/permintaan/items/{item_id}', [PermintaanItemController::class, 'update']);
        Route::delete('/permintaan/items/{item_id}', [PermintaanItemController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| PBI-21: Manajemen Pesan Internal / Inbox Faskes (Briantara Pradhipta)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/inbox', [InboxController::class, 'index']);
    Route::post('/inbox', [InboxController::class, 'store']);
    Route::get('/inbox/recipients', [InboxController::class, 'recipients']);
    Route::get('/inbox/{id}', [InboxController::class, 'show']);
    Route::put('/inbox/{id}', [InboxController::class, 'update']);
    Route::delete('/inbox/{id}', [InboxController::class, 'destroy']);
});
