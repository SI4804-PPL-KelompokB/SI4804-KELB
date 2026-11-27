<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInboxRequest;
use App\Http\Requests\UpdateInboxRequest;
use App\Models\Inbox;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Web Controller untuk Tampilan Pesan Internal / Inbox Faskes (PBI-21 / F22).
 * Menangani navigasi, form kirim pesan, serta antarmuka kotak masuk berbasis Blade.
 */
class InboxController extends Controller
{
    /**
     * GET /inbox
     * Menampilkan halaman kotak masuk & pesan terkirim.
     */
    public function index(Request $request): View
    {
        $userId = (int) Auth::id();
        $folder = $request->query('folder', 'inbox');
        $status = $request->query('status');
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

        if (! empty($search)) {
            $query->search($search);
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        $unreadCount = Inbox::inboxFor($userId)->unread()->count();
        $totalInboxCount = Inbox::inboxFor($userId)->count();
        $totalSentCount = Inbox::sentBy($userId)->count();

        // Daftar penerima yang valid untuk modal kirim pesan baru
        $recipients = User::query()
            ->where('id', '!=', $userId)
            ->where('is_active', true)
            ->with('faskes:id,nama_faskes,jenis')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'jabatan', 'faskes_id']);

        return view('inbox.index', compact(
            'messages',
            'folder',
            'status',
            'search',
            'unreadCount',
            'totalInboxCount',
            'totalSentCount',
            'recipients'
        ));
    }

    /**
     * POST /inbox
     * Mengirim pesan baru dari form web.
     */
    public function store(StoreInboxRequest $request): RedirectResponse
    {
        $userId = (int) Auth::id();

        $inbox = Inbox::create([
            'pengirim_id' => $userId,
            'penerima_id' => (int) $request->validated('penerima_id'),
            'subjek' => $request->validated('subjek'),
            'isi' => $request->validated('isi'),
            'is_read' => false,
            'read_at' => null,
            'deleted_by_pengirim' => false,
            'deleted_by_penerima' => false,
        ]);

        $penerima = User::find($inbox->penerima_id);
        $namaPenerima = $penerima ? $penerima->name : 'Penerima';

        return redirect()->route('inbox.index', ['folder' => 'sent'])
            ->with('success', "Pesan berhasil dikirim kepada {$namaPenerima}.");
    }

    /**
     * PUT /inbox/{id}
     * Memperbarui status baca pesan dari web (toggle read/unread).
     */
    public function update(UpdateInboxRequest $request, int $id): RedirectResponse
    {
        $userId = (int) Auth::id();
        $inbox = Inbox::findOrFail($id);

        if ($inbox->penerima_id !== $userId) {
            return redirect()->route('inbox.index')
                ->with('error', 'Anda tidak berhak mengubah status baca pesan ini.');
        }

        $isRead = (bool) $request->validated('is_read');
        if ($isRead) {
            $inbox->markAsRead();
            $msg = 'Pesan ditandai sebagai sudah dibaca.';
        } else {
            $inbox->markAsUnread();
            $msg = 'Pesan ditandai sebagai belum dibaca.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * DELETE /inbox/{id}
     * Menghapus pesan dari kotak pesan pengguna.
     */
    public function destroy(int $id): RedirectResponse
    {
        $userId = (int) Auth::id();
        $inbox = Inbox::findOrFail($id);

        if ($inbox->pengirim_id !== $userId && $inbox->penerima_id !== $userId) {
            return redirect()->route('inbox.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menghapus pesan ini.');
        }

        $inbox->deleteForUser($userId);

        return redirect()->back()
            ->with('success', 'Pesan berhasil dihapus dari kotak pesan Anda.');
    }
}
