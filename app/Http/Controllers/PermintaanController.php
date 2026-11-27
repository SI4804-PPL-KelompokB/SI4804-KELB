<?php

namespace App\Http\Controllers;

use App\Models\Urgensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Web Controller untuk Halaman Antarmuka Permintaan Darurat Faskes (PBI-07).
 *
 * Seluruh operasi CRUD dan validasi tetap ditangani oleh API stateful
 * (/api/permintaan) agar logika bisnis tidak terduplikasi.
 */
class PermintaanController extends Controller
{
    /**
     * GET /permintaan
     * Menampilkan halaman antarmuka Permintaan Darurat.
     */
    public function index(): View
    {
        $user = Auth::user();
        $urgensiList = Urgensi::orderBy('batas_waktu_jam')
            ->orderBy('nama_level')
            ->get(['id', 'nama_level', 'batas_waktu_jam']);

        $canManage = $user->hasRole('faskes')
            && (bool) $user->is_active
            && $user->faskes_id !== null;

        return view('permintaan.index', compact('urgensiList', 'canManage'));
    }
}
