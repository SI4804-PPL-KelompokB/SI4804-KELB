@extends('layouts.dashboard')

@section('title', 'Manajemen Wilayah - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        {{-- Decorative background accent --}}
        <div class="absolute inset-y-0 right-0 w-64 bg-gradient-to-l from-teal-50 to-transparent pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>PBI-01</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-slate-500 normal-case font-medium">Manajemen Data Wilayah / Zona Faskes</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Data Wilayah
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola pengelompokan geografis zona fasilitas kesehatan di wilayah Kota Bandung.
                </p>
            </div>

            @if ($canManage)
            {{-- Tombol Tambah Wilayah (hanya admin & dinkes) --}}
            <div class="shrink-0">
                <button
                    id="btn-open-modal-tambah"
                    onclick="openModalTambah()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-sm hover:shadow-md transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Wilayah
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== STATS SUMMARY ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Wilayah</p>
                <p class="text-2xl font-extrabold text-slate-900">{{ $wilayah->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Cakupan</p>
                <p class="text-sm font-bold text-slate-800 leading-tight">Kota Bandung</p>
            </div>
        </div>
    </div>

    {{-- ===== TABEL DAFTAR WILAYAH ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span>Daftar Wilayah</span>
                <span class="text-xs font-normal text-slate-400">({{ $wilayah->count() }} zona terdaftar)</span>
            </h2>
            @if ($canManage)
            <span class="text-[11px] text-slate-400 font-medium">
                Anda memiliki akses kelola penuh
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-[11px] font-medium">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Hanya Lihat
            </span>
            @endif
        </div>

        @if ($wilayah->isEmpty())
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-700">Belum ada data wilayah</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-xs">
                Belum ada wilayah yang terdaftar dalam sistem. {{ $canManage ? 'Mulai tambahkan wilayah pertama.' : 'Hubungi Admin atau Dinkes untuk menambahkan wilayah.' }}
            </p>
            @if ($canManage)
            <button
                onclick="openModalTambah()"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition cursor-pointer"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Wilayah Pertama
            </button>
            @endif
        </div>
        @else
        {{-- Data Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabel-wilayah">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100">
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 w-12">#</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Nama Wilayah</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Deskripsi</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Dibuat</th>
                        @if ($canManage)
                        <th class="text-center px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($wilayah as $index => $item)
                    <tr class="hover:bg-slate-50/50 transition-colors group" id="row-wilayah-{{ $item->id }}">
                        <td class="px-6 py-4 text-xs text-slate-400 font-mono">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 font-bold text-xs">
                                    {{ strtoupper(substr($item->nama_wilayah, 0, 2)) }}
                                </div>
                                <span class="font-semibold text-slate-800">{{ $item->nama_wilayah }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 max-w-xs">
                            <span class="line-clamp-2">{{ $item->deskripsi ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400 font-mono whitespace-nowrap">
                            {{ $item->created_at->format('d/m/Y') }}
                        </td>
                        @if ($canManage)
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Edit --}}
                                <button
                                    id="btn-edit-{{ $item->id }}"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_wilayah }}"
                                    data-deskripsi="{{ $item->deskripsi ?? '' }}"
                                    onclick="openModalEdit(this)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-xs font-semibold transition cursor-pointer"
                                    title="Edit wilayah"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>

                                {{-- Tombol Hapus --}}
                                <button
                                    id="btn-hapus-{{ $item->id }}"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_wilayah }}"
                                    onclick="confirmHapus(this)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-semibold transition cursor-pointer"
                                    title="Hapus wilayah"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- ============================================================ --}}
{{-- MODAL & FORM SECTION (hanya untuk admin & dinkes)            --}}
{{-- ============================================================ --}}
@if ($canManage)

{{-- ===== MODAL TAMBAH WILAYAH ===== --}}
<div
    id="modal-tambah"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-tambah-title"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalTambah()"></div>

    {{-- Modal Panel --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 animate-fade-in">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div>
                <h2 id="modal-tambah-title" class="text-base font-bold text-slate-900">Tambah Wilayah Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Isi data wilayah/zona faskes yang akan ditambahkan.</p>
            </div>
            <button onclick="closeModalTambah()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition cursor-pointer" aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="form-tambah-wilayah" action="{{ route('wilayah.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf

            {{-- Nama Wilayah --}}
            <div>
                <label for="tambah-nama_wilayah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Wilayah <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="tambah-nama_wilayah"
                    name="nama_wilayah"
                    placeholder="Contoh: Wilayah Bandung Utara"
                    maxlength="255"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition"
                    required
                    autocomplete="off"
                >
                <p id="tambah-nama_wilayah-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="tambah-deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Deskripsi <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <textarea
                    id="tambah-deskripsi"
                    name="deskripsi"
                    rows="3"
                    maxlength="1000"
                    placeholder="Keterangan singkat mengenai cakupan wilayah ini..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition resize-none"
                ></textarea>
            </div>

            {{-- Error umum form --}}
            <div id="tambah-form-error" class="hidden p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs"></div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="button"
                    onclick="closeModalTambah()"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btn-submit-tambah"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Wilayah
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDIT WILAYAH ===== --}}
<div
    id="modal-edit"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-edit-title"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalEdit()"></div>

    {{-- Modal Panel --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div>
                <h2 id="modal-edit-title" class="text-base font-bold text-slate-900">Edit Wilayah</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi wilayah yang dipilih.</p>
            </div>
            <button onclick="closeModalEdit()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition cursor-pointer" aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form (action URL diset via JS) --}}
        <form id="form-edit-wilayah" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-wilayah-id" name="_wilayah_id" value="">

            {{-- Nama Wilayah --}}
            <div>
                <label for="edit-nama_wilayah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Wilayah <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="edit-nama_wilayah"
                    name="nama_wilayah"
                    maxlength="255"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition"
                    required
                    autocomplete="off"
                >
                <p id="edit-nama_wilayah-error" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="edit-deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Deskripsi <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <textarea
                    id="edit-deskripsi"
                    name="deskripsi"
                    rows="3"
                    maxlength="1000"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition resize-none"
                ></textarea>
            </div>

            {{-- Error umum form --}}
            <div id="edit-form-error" class="hidden p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs"></div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="button"
                    onclick="closeModalEdit()"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btn-submit-edit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
<div
    id="modal-hapus"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-hapus-title"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalHapus()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm border border-slate-200">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h2 id="modal-hapus-title" class="text-base font-bold text-slate-900">Konfirmasi Penghapusan</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Apakah Anda yakin ingin menghapus wilayah
                <strong id="hapus-nama-wilayah" class="text-slate-800"></strong>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <form id="form-hapus-wilayah" method="POST" class="px-6 pb-6">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button
                    type="button"
                    onclick="closeModalHapus()"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btn-submit-hapus"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-sm transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

@endif {{-- end @if ($canManage) --}}

{{-- ===== VALIDASI ERROR DARI SERVER (setelah form submit) ===== --}}
@if ($errors->any())
<script>
    // Jika ada validation errors, otomatis buka modal tambah kembali
    document.addEventListener('DOMContentLoaded', function () {
        @if (old('_method') === 'PUT')
            // Error dari edit form — buka modal edit
            // Data lama disimpan di session, tampilkan pesan error umum
            showFlashMessage('error', 'Terjadi kesalahan validasi. Silakan periksa kembali data yang diisi.');
        @else
            openModalTambah();
            showFlashMessage('error', @js($errors->first()));
        @endif
    });
</script>
@endif

{{-- ===== JAVASCRIPT: MODAL & INTERAKSI ===== --}}
<script>
    const wilayahBaseUrl = @json(route('wilayah.index'));
    // ── Tambah Modal ──────────────────────────────────────────────
    function openModalTambah() {
        document.getElementById('modal-tambah').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('tambah-nama_wilayah').focus(), 50);
    }

    function closeModalTambah() {
        document.getElementById('modal-tambah').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('form-tambah-wilayah').reset();
        clearFormError('tambah');
    }

    // ── Edit Modal ────────────────────────────────────────────────
    function openModalEdit(el) {
        var id = el.dataset.id;
        var nama = el.dataset.nama;
        var deskripsi = el.dataset.deskripsi;
        document.getElementById('edit-wilayah-id').value = id;
        document.getElementById('edit-nama_wilayah').value = nama;
        document.getElementById('edit-deskripsi').value = deskripsi;
        document.getElementById('form-edit-wilayah').action = wilayahBaseUrl + '/' + encodeURIComponent(id);
        clearFormError('edit');
        document.getElementById('modal-edit').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('edit-nama_wilayah').focus(), 50);
    }

    function closeModalEdit() {
        document.getElementById('modal-edit').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // ── Hapus Modal ───────────────────────────────────────────────
    function confirmHapus(el) {
        var id = el.dataset.id;
        var nama = el.dataset.nama;
        document.getElementById('hapus-nama-wilayah').textContent = '"' + nama + '"';
        document.getElementById('form-hapus-wilayah').action = wilayahBaseUrl + '/' + encodeURIComponent(id);
        document.getElementById('modal-hapus').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalHapus() {
        document.getElementById('modal-hapus').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // ── Helpers ───────────────────────────────────────────────────
    function clearFormError(prefix) {
        const errDiv = document.getElementById(prefix + '-form-error');
        if (errDiv) { errDiv.textContent = ''; errDiv.classList.add('hidden'); }
        const namaErr = document.getElementById(prefix + '-nama_wilayah-error');
        if (namaErr) { namaErr.textContent = ''; namaErr.classList.add('hidden'); }
    }

    function showFlashMessage(type, message) {
        const cls = type === 'success'
            ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
            : 'bg-red-50 border-red-200 text-red-800';
        const div = document.createElement('div');
        div.className = 'mb-6 p-4 rounded-xl ' + cls + ' border text-sm flex items-center justify-between';

        const span = document.createElement('span');
        span.textContent = message;
        div.appendChild(span);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ml-4 font-bold text-lg leading-none opacity-70 hover:opacity-100';
        btn.textContent = '\u00D7';
        btn.addEventListener('click', function () { div.remove(); });
        div.appendChild(btn);

        const main = document.querySelector('main') || document.body;
        main.prepend(div);
    }

    // ── Keyboard ESC tutup modal ──────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModalTambah();
            closeModalEdit();
            closeModalHapus();
        }
    });
</script>
@endsection
