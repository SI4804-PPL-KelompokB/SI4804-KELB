<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermintaanRequest;
use App\Http\Requests\UpdatePermintaanRequest;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller untuk Pencatatan Permintaan Darurat Faskes (PBI-07).
 *
 * Konvensi response JSON (selaras dengan PBI-01):
 *   Sukses : { "status": "success", "message": "...", "data": {...} }
 *   Error  : { "status": "error",   "message": "...", "errors": {...} }
 */
class PermintaanController extends Controller
{
    /**
     * Relasi terbatas yang dimuat pada response API agar tidak mengekspos data sensitif user.
     *
     * @var array<int, string>
     */
    private const API_RELATIONS = [
        'urgensi:id,nama_level,batas_waktu_jam',
        'faskes:id,nama_faskes,jenis,kota',
    ];

    /**
     * GET /api/permintaan
     * Menampilkan riwayat permintaan darurat.
     * - Role faskes: hanya permintaan milik faskes pengguna yang terautentikasi.
     * - Role dinkes & admin: seluruh permintaan lintas fasilitas kesehatan.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $query = Permintaan::with(self::API_RELATIONS)->latest();

        if ($user->hasRole('faskes')) {
            $query->where('faskes_id', $user->faskes_id);
        }

        $permintaan = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar permintaan darurat berhasil diambil.',
            'data' => $permintaan,
        ]);
    }

    /**
     * POST /api/permintaan
     * Membuat header permintaan darurat baru untuk faskes pengguna yang terautentikasi.
     * Izin: faskes.
     */
    public function store(StorePermintaanRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $permintaan = Permintaan::create([
            'faskes_id' => $user->faskes_id,
            'urgensi_id' => $validated['urgensi_id'],
            'user_id' => $user->id,
            'status' => Permintaan::STATUS_DIAJUKAN,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $permintaan->load(self::API_RELATIONS);

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan darurat berhasil dibuat.',
            'data' => $permintaan,
        ], 201);
    }

    /**
     * GET /api/permintaan/{id}
     * Menampilkan detail satu permintaan darurat.
     * - Role faskes: hanya dapat mengakses permintaan milik faskes sendiri (mengembalikan 404 untuk faskes lain).
     * - Role dinkes & admin: dapat mengakses detail permintaan lintas fasilitas.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $query = Permintaan::with(self::API_RELATIONS);

        if ($user->hasRole('faskes')) {
            $query->where('faskes_id', $user->faskes_id);
        }

        $permintaan = $query->find($id);

        if (! $permintaan) {
            return response()->json([
                'status' => 'error',
                'message' => "Permintaan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail permintaan darurat berhasil diambil.',
            'data' => $permintaan,
        ]);
    }

    /**
     * PUT /api/permintaan/{id}
     * Memperbarui tingkat urgensi permintaan darurat milik faskes pengguna.
     * Izin: faskes pemilik permintaan dengan status 'diajukan'.
     */
    public function update(UpdatePermintaanRequest $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $permintaan = Permintaan::where('faskes_id', $user->faskes_id)->find($id);

        if (! $permintaan) {
            return response()->json([
                'status' => 'error',
                'message' => "Permintaan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        if (! $permintaan->isEditable()) {
            return response()->json([
                'status' => 'error',
                'message' => "Permintaan dengan status \"{$permintaan->status}\" tidak dapat diubah tingkat urgensinya.",
            ], 422);
        }

        $permintaan->update([
            'urgensi_id' => $request->validated('urgensi_id'),
        ]);

        $permintaan->load(self::API_RELATIONS);

        return response()->json([
            'status' => 'success',
            'message' => 'Tingkat urgensi permintaan darurat berhasil diperbarui.',
            'data' => $permintaan,
        ]);
    }

    /**
     * DELETE /api/permintaan/{id}
     * Menghapus permintaan darurat milik faskes pengguna.
     * Izin: faskes pemilik permintaan dengan status 'diajukan'.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $permintaan = Permintaan::where('faskes_id', $user->faskes_id)->find($id);

        if (! $permintaan) {
            return response()->json([
                'status' => 'error',
                'message' => "Permintaan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        if (! $permintaan->isEditable()) {
            return response()->json([
                'status' => 'error',
                'message' => "Permintaan dengan status \"{$permintaan->status}\" tidak dapat dihapus.",
            ], 422);
        }

        $permintaan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan darurat berhasil dihapus.',
            'data' => null,
        ]);
    }

    /**
     * Memastikan akun aktif dan (khusus role faskes) terhubung ke sebuah fasilitas kesehatan.
     */
    private function guardActiveAccount(User $user): ?JsonResponse
    {
        if (! $user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Anda tidak aktif. Hubungi administrator sistem.',
            ], 403);
        }

        if ($user->hasRole('faskes') && $user->faskes_id === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Anda belum terhubung dengan fasilitas kesehatan.',
            ], 403);
        }

        return null;
    }
}
