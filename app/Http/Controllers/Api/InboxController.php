<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInboxRequest;
use App\Http\Requests\UpdateInboxRequest;
use App\Models\Inbox;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * API Controller untuk Manajemen Pesan Internal / Inbox Faskes (PBI-21 / F22).
 *
 * Konvensi response JSON:
 *   Sukses : { "status": "success", "message": "...", "data": {...} }
 *   Error  : { "status": "error",   "message": "...", "errors": {...} }
 */
class InboxController extends Controller
{
    /**
     * GET /api/inbox
     * Menampilkan daftar pesan kotak masuk (inbox) atau pesan terkirim (sent).
     * Izin: semua role yang terautentikasi.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = (int) Auth::id();
        $folder = $request->query('folder', 'inbox'); // 'inbox' atau 'sent'
        $status = $request->query('status');          // 'read', 'unread', atau null
        $search = $request->query('search');

        $query = Inbox::query()->with([
            'pengirim:id,name,role,faskes_id',
            'pengirim.faskes:id,nama_faskes',
            'penerima:id,name,role,faskes_id',
            'penerima.faskes:id,nama_faskes',
        ]);

        if ($folder === 'sent') {
            $query->sentBy($userId);
        } else {
            $query->inboxFor($userId);

            if ($status === 'unread') {
                $query->where('is_read', false);
            } elseif ($status === 'read') {
                $query->where('is_read', true);
            }
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $messages = $query->latest()->get();

        $unreadCount = Inbox::inboxFor($userId)->unread()->count();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar pesan berhasil diambil.',
            'data'    => [
                'folder'       => $folder,
                'unread_count' => $unreadCount,
                'total'        => $messages->count(),
                'messages'     => $messages,
            ],
        ]);
    }

    /**
     * POST /api/inbox
     * Mengirim pesan baru ke pengguna lain (Faskes, Dinkes, Penyedia, Admin).
     * Izin: semua role yang terautentikasi.
     */
    public function store(StoreInboxRequest $request): JsonResponse
    {
        $userId = (int) Auth::id();

        $inbox = Inbox::create([
            'pengirim_id'         => $userId,
            'penerima_id'         => (int) $request->validated('penerima_id'),
            'subjek'              => $request->validated('subjek'),
            'isi'                 => $request->validated('isi'),
            'is_read'             => false,
            'read_at'             => null,
            'deleted_by_pengirim' => false,
            'deleted_by_penerima' => false,
        ]);

        $inbox->load([
            'pengirim:id,name,role,faskes_id',
            'pengirim.faskes:id,nama_faskes',
            'penerima:id,name,role,faskes_id',
            'penerima.faskes:id,nama_faskes',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pesan berhasil dikirim.',
            'data'    => $inbox,
        ], 201);
    }

    /**
     * GET /api/inbox/recipients
     * Menampilkan daftar pengguna yang dapat dikirimi pesan (selain diri sendiri).
     * Izin: semua role yang terautentikasi.
     */
    public function recipients(): JsonResponse
    {
        $currentUserId = (int) Auth::id();

        $recipients = User::query()
            ->where('id', '!=', $currentUserId)
            ->where('is_active', true)
            ->with('faskes:id,nama_faskes,jenis')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'jabatan', 'faskes_id']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar penerima berhasil diambil.',
            'data'    => $recipients,
        ]);
    }

    /**
     * GET /api/inbox/{id}
     * Menampilkan detail satu pesan.
     * Izin: pengirim atau penerima pesan.
     * Jika dibuka oleh penerima, otomatis tandai sebagai sudah dibaca (read).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $userId = (int) Auth::id();
        $inbox = Inbox::with([
            'pengirim:id,name,role,faskes_id',
            'pengirim.faskes:id,nama_faskes',
            'penerima:id,name,role,faskes_id',
            'penerima.faskes:id,nama_faskes',
        ])->find($id);

        if (!$inbox) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        // Cek otorisasi akses
        if ($inbox->pengirim_id !== $userId && $inbox->penerima_id !== $userId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk membaca pesan ini.',
            ], 403);
        }

        // Cek jika pesan telah dihapus oleh pengguna yang sedang meminta
        if ($inbox->penerima_id === $userId && $inbox->deleted_by_penerima) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        if ($inbox->pengirim_id === $userId && $inbox->deleted_by_pengirim) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        // Otomatis tandai sebagai sudah dibaca jika penerima membuka pesan
        $autoMark = $request->boolean('auto_read', true);
        if ($inbox->penerima_id === $userId && !$inbox->is_read && $autoMark) {
            $inbox->markAsRead();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail pesan berhasil diambil.',
            'data'    => $inbox->fresh([
                'pengirim:id,name,role,faskes_id',
                'pengirim.faskes:id,nama_faskes',
                'penerima:id,name,role,faskes_id',
                'penerima.faskes:id,nama_faskes',
            ]),
        ]);
    }

    /**
     * PUT /api/inbox/{id}
     * Memperbarui status baca pesan (tandai read/unread).
     * Izin: hanya penerima pesan.
     */
    public function update(UpdateInboxRequest $request, int $id): JsonResponse
    {
        $userId = (int) Auth::id();
        $inbox = Inbox::find($id);

        if (!$inbox) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        // Hanya penerima pesan yang dapat mengubah status read/unread
        if ($inbox->penerima_id !== $userId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya penerima pesan yang dapat menandai status baca pesan ini.',
            ], 403);
        }

        if ($inbox->deleted_by_penerima) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        $isRead = (bool) $request->validated('is_read');
        if ($isRead) {
            $inbox->markAsRead();
            $msg = 'Pesan telah ditandai sebagai sudah dibaca.';
        } else {
            $inbox->markAsUnread();
            $msg = 'Pesan telah ditandai sebagai belum dibaca.';
        }

        return response()->json([
            'status'  => 'success',
            'message' => $msg,
            'data'    => $inbox->fresh([
                'pengirim:id,name,role,faskes_id',
                'pengirim.faskes:id,nama_faskes',
                'penerima:id,name,role,faskes_id',
                'penerima.faskes:id,nama_faskes',
            ]),
        ]);
    }

    /**
     * DELETE /api/inbox/{id}
     * Menghapus pesan untuk pengguna yang bersangkutan.
     * Izin: pengirim atau penerima pesan.
     */
    public function destroy(int $id): JsonResponse
    {
        $userId = (int) Auth::id();
        $inbox = Inbox::find($id);

        if (!$inbox) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        if ($inbox->pengirim_id !== $userId && $inbox->penerima_id !== $userId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk menghapus pesan ini.',
            ], 403);
        }

        // Cek jika pesan memang sudah terhapus bagi user ini
        if (($inbox->penerima_id === $userId && $inbox->deleted_by_penerima) ||
            ($inbox->pengirim_id === $userId && $inbox->deleted_by_pengirim)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Pesan dengan ID {$id} sudah dihapus sebelumnya.",
            ], 404);
        }

        $inbox->deleteForUser($userId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pesan berhasil dihapus dari kotak pesan Anda.',
            'data'    => null,
        ]);
    }
}
