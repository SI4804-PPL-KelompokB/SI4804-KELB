<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWilayahRequest;
use App\Http\Requests\UpdateWilayahRequest;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Web Controller untuk Halaman Manajemen Wilayah (PBI-01).
 * Menangani form submission berbasis Blade/HTML, terpisah dari API controller.
 */
class WilayahController extends Controller
{
    /**
     * GET /wilayah
     * Menampilkan halaman daftar wilayah.
     * Izin: semua role yang terautentikasi.
     */
    public function index(): View
    {
        $wilayah = Wilayah::orderBy('nama_wilayah')->get();
        $canManage = Auth::user()->hasRole(['admin', 'dinkes']);

        return view('wilayah.index', compact('wilayah', 'canManage'));
    }

    /**
     * POST /wilayah
     * Menyimpan wilayah baru dari form Blade.
     * Izin: admin, dinkes.
     */
    public function store(StoreWilayahRequest $request): RedirectResponse
    {
        $wilayah = Wilayah::create($request->validated());

        return redirect()->route('wilayah.index')
            ->with('success', "Wilayah \"{$wilayah->nama_wilayah}\" berhasil ditambahkan.");
    }

    /**
     * PUT /wilayah/{id}
     * Memperbarui data wilayah dari form Blade.
     * Izin: admin, dinkes.
     */
    public function update(UpdateWilayahRequest $request, int $id): RedirectResponse
    {
        $wilayah = Wilayah::findOrFail($id);
        $wilayah->update($request->validated());

        return redirect()->route('wilayah.index')
            ->with('success', "Wilayah \"{$wilayah->nama_wilayah}\" berhasil diperbarui.");
    }

    /**
     * DELETE /wilayah/{id}
     * Menghapus data wilayah dari form Blade.
     * Izin: admin, dinkes.
     * Proteksi: wilayah yang masih direferensikan tidak dapat dihapus.
     */
    public function destroy(int $id): RedirectResponse
    {
        $wilayah = Wilayah::findOrFail($id);

        if ($wilayah->isReferenced()) {
            return redirect()->route('wilayah.index')
                ->with('error', "Wilayah \"{$wilayah->nama_wilayah}\" tidak dapat dihapus karena masih digunakan oleh satu atau lebih fasilitas kesehatan.");
        }

        $namaWilayah = $wilayah->nama_wilayah;
        $wilayah->delete();

        return redirect()->route('wilayah.index')
            ->with('success', "Wilayah \"{$namaWilayah}\" berhasil dihapus.");
    }
}
