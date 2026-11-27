<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenawaranRequest;
use App\Http\Requests\UpdatePenawaranRequest;
use App\Models\Penawaran;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller untuk Pencatatan Dokumen Penawaran Surplus (PBI-09).
 */
class PenawaranController extends Controller
{
    private const API_RELATIONS = [
        'faskes:id,nama_faskes,jenis,kota',
    ];

    /**
     * GET /api/penawaran
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $query = Penawaran::with(self::API_RELATIONS)->latest();

        if ($user->hasRole('faskes')) {
            $query->where('faskes_id', $user->faskes_id);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar dokumen penawaran surplus berhasil diambil.',
            'data' => $query->get(),
        ]);
    }

    /**
     * POST /api/penawaran
     */
    public function store(StorePenawaranRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $judul = isset($validated['judul']) && trim((string) $validated['judul']) !== ''
            ? trim((string) $validated['judul'])
            : 'Penawaran Surplus Obat';

        $catatan = isset($validated['catatan']) && trim((string) $validated['catatan']) !== ''
            ? trim((string) $validated['catatan'])
            : null;

        $penawaran = Penawaran::create([
            'faskes_id' => $user->faskes_id,
            'user_id' => $user->id,
            'judul' => $judul,
            'status' => Penawaran::STATUS_TERSEDIA,
            'catatan' => $catatan,
        ]);

        $penawaran->load(self::API_RELATIONS);

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen penawaran surplus berhasil dibuat.',
            'data' => $penawaran,
        ], 201);
    }

    /**
     * GET /api/penawaran/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $query = Penawaran::with(self::API_RELATIONS);

        if ($user->hasRole('faskes')) {
            $query->where('faskes_id', $user->faskes_id);
        }

        $penawaran = $query->find($id);

        if (! $penawaran) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokumen penawaran dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail dokumen penawaran surplus berhasil diambil.',
            'data' => $penawaran,
        ]);
    }

    /**
     * PUT /api/penawaran/{id}
     */
    public function update(UpdatePenawaranRequest $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $penawaran = Penawaran::where('faskes_id', $user->faskes_id)->find($id);

        if (! $penawaran) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokumen penawaran dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        if (! $penawaran->isEditable()) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokumen penawaran dengan status \"{$penawaran->status}\" tidak dapat diubah.",
            ], 422);
        }

        $validated = $request->validated();
        $catatanRaw = $validated['catatan'];
        $updates = [
            'catatan' => ($catatanRaw !== null && trim((string) $catatanRaw) !== '')
                ? trim((string) $catatanRaw)
                : null,
        ];

        if (array_key_exists('judul', $validated) && trim((string) $validated['judul']) !== '') {
            $updates['judul'] = trim((string) $validated['judul']);
        }

        $penawaran->update($updates);
        $penawaran->load(self::API_RELATIONS);

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan dokumen penawaran surplus berhasil diperbarui.',
            'data' => $penawaran,
        ]);
    }

    /**
     * DELETE /api/penawaran/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $penawaran = Penawaran::where('faskes_id', $user->faskes_id)->find($id);

        if (! $penawaran) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokumen penawaran dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        if (! $penawaran->isEditable()) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokumen penawaran dengan status \"{$penawaran->status}\" tidak dapat dihapus.",
            ], 422);
        }

        $penawaran->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen penawaran surplus berhasil dihapus.',
            'data' => null,
        ]);
    }

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
