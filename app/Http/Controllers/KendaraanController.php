<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    /**
     * PBI-14: Menampilkan daftar kendaraan/kurir internal milik penyedia
     * (R: Read - Lihat daftar)
     */
    public function index(): View
    {
        $user = Auth::user()->load('faskes');
        $faskesId = $user->faskes_id;

        $kendaraan = Kendaraan::where('faskes_id', $faskesId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penyedia.kendaraan.index', compact('user', 'kendaraan'));
    }

    /**
     * PBI-14: Form tambah kendaraan/kurir baru
     * (C: Create - Tambah kurir/ambulans)
     */
    public function create(): View
    {
        $user = Auth::user()->load('faskes');
        $jenisKendaraan = Kendaraan::JENIS_KENDARAAN;

        return view('penyedia.kendaraan.create', compact('user', 'jenisKendaraan'));
    }

    /**
     * PBI-14: Simpan data kendaraan/kurir baru ke database
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_kurir' => ['required', 'string', 'max:255'],
            'jenis_kendaraan' => ['required', 'string', 'in:' . implode(',', Kendaraan::JENIS_KENDARAAN)],
            'plat_nomor' => ['required', 'string', 'max:20', 'unique:kendaraan,plat_nomor'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:aktif,nonaktif,maintenance'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kurir.required' => 'Nama kurir wajib diisi.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih.',
            'jenis_kendaraan.in' => 'Jenis kendaraan yang dipilih tidak valid.',
            'plat_nomor.required' => 'Plat nomor wajib diisi.',
            'plat_nomor.unique' => 'Plat nomor sudah terdaftar di sistem.',
            'status.required' => 'Status kendaraan wajib dipilih.',
            'status.in' => 'Status kendaraan yang dipilih tidak valid.',
        ]);

        $validated['faskes_id'] = $user->faskes_id;

        Kendaraan::create($validated);

        return redirect()->route('penyedia.kendaraan.index')
            ->with('success', 'Data kendaraan/kurir "' . $validated['nama_kurir'] . '" berhasil ditambahkan.');
    }

    /**
     * PBI-14: Form edit data kendaraan/kurir
     * (U: Update - Edit plat nomor dan data lainnya)
     */
    public function edit(Kendaraan $kendaraan): View|RedirectResponse
    {
        $user = Auth::user()->load('faskes');

        // Pastikan kendaraan milik faskes penyedia ini
        if ($kendaraan->faskes_id !== $user->faskes_id) {
            return redirect()->route('penyedia.kendaraan.index')
                ->with('error', 'Akses ditolak: Kendaraan ini bukan milik fasilitas Anda.');
        }

        $jenisKendaraan = Kendaraan::JENIS_KENDARAAN;

        return view('penyedia.kendaraan.edit', compact('user', 'kendaraan', 'jenisKendaraan'));
    }

    /**
     * PBI-14: Update data kendaraan/kurir di database
     */
    public function update(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan kendaraan milik faskes penyedia ini
        if ($kendaraan->faskes_id !== $user->faskes_id) {
            return redirect()->route('penyedia.kendaraan.index')
                ->with('error', 'Akses ditolak: Kendaraan ini bukan milik fasilitas Anda.');
        }

        $validated = $request->validate([
            'nama_kurir' => ['required', 'string', 'max:255'],
            'jenis_kendaraan' => ['required', 'string', 'in:' . implode(',', Kendaraan::JENIS_KENDARAAN)],
            'plat_nomor' => ['required', 'string', 'max:20', 'unique:kendaraan,plat_nomor,' . $kendaraan->id],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:aktif,nonaktif,maintenance'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kurir.required' => 'Nama kurir wajib diisi.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib dipilih.',
            'jenis_kendaraan.in' => 'Jenis kendaraan yang dipilih tidak valid.',
            'plat_nomor.required' => 'Plat nomor wajib diisi.',
            'plat_nomor.unique' => 'Plat nomor sudah terdaftar oleh kendaraan lain.',
            'status.required' => 'Status kendaraan wajib dipilih.',
            'status.in' => 'Status kendaraan yang dipilih tidak valid.',
        ]);

        $kendaraan->update($validated);

        return redirect()->route('penyedia.kendaraan.index')
            ->with('success', 'Data kendaraan/kurir "' . $validated['nama_kurir'] . '" berhasil diperbarui.');
    }

    /**
     * PBI-14: Hapus data kendaraan/kurir
     * (D: Delete - Hapus kurir)
     */
    public function destroy(Kendaraan $kendaraan): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan kendaraan milik faskes penyedia ini
        if ($kendaraan->faskes_id !== $user->faskes_id) {
            return redirect()->route('penyedia.kendaraan.index')
                ->with('error', 'Akses ditolak: Kendaraan ini bukan milik fasilitas Anda.');
        }

        $namaKurir = $kendaraan->nama_kurir;
        $kendaraan->delete();

        return redirect()->route('penyedia.kendaraan.index')
            ->with('success', 'Data kendaraan/kurir "' . $namaKurir . '" berhasil dihapus dari sistem.');
    }
}
