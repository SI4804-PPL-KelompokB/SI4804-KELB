<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWilayahRequest;
use App\Http\Requests\UpdateWilayahRequest;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;

/**
 * API Controller untuk Manajemen Data Wilayah (PBI-01).
 *
 * Konvensi response JSON:
 *   Sukses : { "status": "success", "message": "...", "data": {...} }
 *   Error  : { "status": "error",   "message": "...", "errors": {...} }
 */
class WilayahController extends Controller
{
    /**
     * GET /api/wilayah
     * Menampilkan daftar seluruh wilayah.
     * Izin: semua role yang terautentikasi.
     */
    public function index(): JsonResponse
    {
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar wilayah berhasil diambil.',
            'data' => $wilayah,
        ]);
    }

    /**
     * POST /api/wilayah
     * Membuat data wilayah baru.
     * Izin: admin, dinkes.
     */
    public function store(StoreWilayahRequest $request): JsonResponse
    {
        $wilayah = Wilayah::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => "Wilayah \"{$wilayah->nama_wilayah}\" berhasil ditambahkan.",
            'data' => $wilayah,
        ], 201);
    }

    /**
     * GET /api/wilayah/{id}
     * Menampilkan detail satu wilayah.
     * Izin: semua role yang terautentikasi.
     */
    public function show(int $id): JsonResponse
    {
        $wilayah = Wilayah::find($id);

        if (! $wilayah) {
            return response()->json([
                'status' => 'error',
                'message' => "Wilayah dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail wilayah berhasil diambil.',
            'data' => $wilayah,
        ]);
    }

    /**
     * PUT /api/wilayah/{id}
     * Memperbarui data wilayah.
     * Izin: admin, dinkes.
     */
    public function update(UpdateWilayahRequest $request, int $id): JsonResponse
    {
        $wilayah = Wilayah::find($id);

        if (! $wilayah) {
            return response()->json([
                'status' => 'error',
                'message' => "Wilayah dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        $wilayah->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => "Wilayah \"{$wilayah->nama_wilayah}\" berhasil diperbarui.",
            'data' => $wilayah->fresh(),
        ]);
    }

    /**
     * DELETE /api/wilayah/{id}
     * Menghapus data wilayah.
     * Izin: admin, dinkes.
     * Proteksi: wilayah yang masih direferensikan oleh faskes tidak dapat dihapus.
     */
    public function destroy(int $id): JsonResponse
    {
        $wilayah = Wilayah::find($id);

        if (! $wilayah) {
            return response()->json([
                'status' => 'error',
                'message' => "Wilayah dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        // Proteksi referensi: cegah penghapusan jika wilayah masih dipakai
        if ($wilayah->isReferenced()) {
            return response()->json([
                'status' => 'error',
                'message' => "Wilayah \"{$wilayah->nama_wilayah}\" tidak dapat dihapus karena masih digunakan oleh satu atau lebih fasilitas kesehatan.",
            ], 422);
        }

        $namaWilayah = $wilayah->nama_wilayah;
        $wilayah->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Wilayah \"{$namaWilayah}\" berhasil dihapus.",
            'data' => null,
        ]);
    }
}
