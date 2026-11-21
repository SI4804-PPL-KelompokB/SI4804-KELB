<?php

use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - MedStock.id
|--------------------------------------------------------------------------
| Autentikasi menggunakan session Laravel (session-based auth).
| Permintaan tanpa autentikasi mengembalikan JSON 401 (bukan redirect),
| karena route berada dalam prefix 'api' yang dideteksi sebagai API request.
|
| Proteksi CSRF: Request yang mengubah data dari browser harus menyertakan
| CSRF token melalui header X-XSRF-TOKEN. Axios dapat mengirim header ini
| otomatis jika cookie XSRF-TOKEN tersedia, sedangkan Fetch perlu mengaturnya
| secara eksplisit.
*/

/*
|--------------------------------------------------------------------------
| PBI-01: Manajemen Data Wilayah
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // READ: Semua role yang terautentikasi dapat melihat wilayah
    Route::get('/wilayah', [WilayahController::class, 'index']);
    Route::get('/wilayah/{id}', [WilayahController::class, 'show']);

    // WRITE: Hanya admin dan dinkes yang dapat mengelola wilayah
    Route::middleware('role:admin,dinkes')->group(function () {
        Route::post('/wilayah', [WilayahController::class, 'store']);
        Route::put('/wilayah/{id}', [WilayahController::class, 'update']);
        Route::delete('/wilayah/{id}', [WilayahController::class, 'destroy']);
    });
});
