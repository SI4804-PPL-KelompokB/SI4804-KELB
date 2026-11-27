<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermintaanItemRequest;
use App\Http\Requests\UpdatePermintaanItemRequest;
use App\Models\Permintaan;
use App\Models\PermintaanItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller untuk Pengelolaan Detail Item Obat pada Permintaan (PBI-08).
 */
class PermintaanItemController extends Controller
{
    /**
     * GET /api/permintaan/{id}/items
     */
    public function index(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $query = Permintaan::query();
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

        $items = $permintaan->items()->orderBy('id')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar item obat permintaan berhasil diambil.',
            'data' => $items,
        ]);
    }

    /**
     * POST /api/permintaan/{id}/items
     */
    public function store(StorePermintaanItemRequest $request, int $id): JsonResponse
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
                'message' => "Permintaan dengan status \"{$permintaan->status}\" tidak dapat ditambahkan item obat.",
            ], 422);
        }

        $validated = $request->validated();

        $item = $permintaan->items()->create([
            'nama_obat' => trim($validated['nama_obat']),
            'satuan' => isset($validated['satuan']) && trim((string) $validated['satuan']) !== ''
                ? trim((string) $validated['satuan'])
                : 'Box',
            'qty' => (int) $validated['qty'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Item obat berhasil ditambahkan ke permintaan.',
            'data' => $item,
        ], 201);
    }

    /**
     * PUT /api/permintaan/items/{item_id}
     */
    public function update(UpdatePermintaanItemRequest $request, int $itemId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $item = PermintaanItem::with('permintaan')
            ->whereHas('permintaan', fn ($q) => $q->where('faskes_id', $user->faskes_id))
            ->find($itemId);

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => "Item obat dengan ID {$itemId} tidak ditemukan.",
            ], 404);
        }

        if ($item->permintaan === null || ! $item->permintaan->isEditable()) {
            $status = $item->permintaan?->status ?? 'tidak diketahui';

            return response()->json([
                'status' => 'error',
                'message' => "Item obat pada permintaan berstatus \"{$status}\" tidak dapat diubah.",
            ], 422);
        }

        $validated = $request->validated();
        $updates = [
            'qty' => (int) $validated['qty'],
        ];

        if (array_key_exists('nama_obat', $validated)) {
            $updates['nama_obat'] = trim($validated['nama_obat']);
        }

        if (array_key_exists('satuan', $validated) && trim((string) $validated['satuan']) !== '') {
            $updates['satuan'] = trim((string) $validated['satuan']);
        }

        $item->update($updates);

        return response()->json([
            'status' => 'success',
            'message' => 'Jumlah (qty) item obat berhasil diperbarui.',
            'data' => $item->fresh(),
        ]);
    }

    /**
     * DELETE /api/permintaan/items/{item_id}
     */
    public function destroy(Request $request, int $itemId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($guardError = $this->guardActiveAccount($user)) {
            return $guardError;
        }

        $item = PermintaanItem::with('permintaan')
            ->whereHas('permintaan', fn ($q) => $q->where('faskes_id', $user->faskes_id))
            ->find($itemId);

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => "Item obat dengan ID {$itemId} tidak ditemukan.",
            ], 404);
        }

        if ($item->permintaan === null || ! $item->permintaan->isEditable()) {
            $status = $item->permintaan?->status ?? 'tidak diketahui';

            return response()->json([
                'status' => 'error',
                'message' => "Item obat pada permintaan berstatus \"{$status}\" tidak dapat dihapus.",
            ], 422);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item obat berhasil dihapus dari permintaan.',
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
