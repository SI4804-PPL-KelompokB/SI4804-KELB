@extends('layouts.dashboard')

@section('title', 'Permintaan Darurat - MedStock.id')

@section('dashboard_content')
@php
    $hasUrgensi = $urgensiList->isNotEmpty();
    $canCreate = $canManage && $hasUrgensi;
@endphp

<div class="space-y-6">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-72 bg-gradient-to-l from-teal-50/90 to-transparent pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <span>PBI-07</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-500 normal-case font-medium">Pencatatan Permintaan Darurat Faskes</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Permintaan Darurat
                </h1>
                <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                    Catat pengajuan kebutuhan obat darurat untuk fasilitas kesehatan Anda, pantau status pengajuan, dan sesuaikan tingkat urgensi selama permintaan masih berstatus <span class="font-semibold text-slate-700">diajukan</span>.
                </p>
            </div>

            @if ($canManage)
                <div class="shrink-0">
                    <button
                        type="button"
                        id="btn-open-modal-create"
                        onclick="openCreateModal()"
                        @disabled(! $hasUrgensi)
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $hasUrgensi
                            ? 'bg-teal-600 hover:bg-teal-700 text-white shadow-xs hover:shadow cursor-pointer'
                            : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                        title="{{ $hasUrgensi ? 'Buat permintaan darurat baru' : 'Data master tingkat urgensi belum tersedia' }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Permintaan Darurat</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== EMPTY MASTER URGENSI WARNING ===== --}}
    @if ($canManage && ! $hasUrgensi)
        <div id="empty-urgensi-banner" class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="text-xs sm:text-sm">
                <h2 class="font-bold text-amber-950">Master Tingkat Urgensi Belum Tersedia</h2>
                <p class="text-amber-800 mt-0.5 leading-relaxed">
                    Data referensi tingkat urgensi (<code class="px-1.5 py-0.5 rounded bg-amber-100/80 font-mono text-xs">urgensi</code>) belum diisi pada sistem (dikelola melalui PBI-11). Pembuatan permintaan darurat baru dinonaktifkan sementara hingga minimal satu tingkat urgensi tersedia.
                </p>
            </div>
        </div>
    @endif

    {{-- ===== DYNAMIC API FEEDBACK ALERT ===== --}}
    <div id="api-alert-container" class="hidden"></div>

    {{-- ===== STATS SUMMARY ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Permintaan</p>
                <p id="stat-total-requests" class="text-2xl font-extrabold text-slate-900">0</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Diajukan</p>
                <p id="stat-diajukan-requests" class="text-2xl font-extrabold text-slate-900">0</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Cakupan Akses</p>
                <p class="text-sm font-bold text-slate-800 truncate">
                    {{ auth()->user()->faskes->nama_faskes ?? 'Lintas Fasilitas Kesehatan' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ===== REQUEST HISTORY LIST / TABLE ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-800">Riwayat Permintaan Darurat</h2>
                <span id="table-count-badge" class="text-xs text-slate-400">(Memuat...)</span>
            </div>

            <div class="flex items-center gap-2.5">
                @if (! $canManage)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-medium">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Mode Lihat Saja
                    </span>
                @endif

                <button
                    type="button"
                    id="btn-refresh-permintaan"
                    onclick="loadPermintaanList()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition cursor-pointer"
                    title="Muat ulang daftar permintaan"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Muat Ulang</span>
                </button>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="permintaan-loading-state" class="py-16 px-6 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-teal-50 text-teal-600 mb-3 animate-spin">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-700">Memuat data permintaan darurat...</p>
            <p class="text-xs text-slate-400 mt-1">Mengambil riwayat dari server MedStock.id</p>
        </div>

        {{-- Error State --}}
        <div id="permintaan-error-state" class="hidden py-16 px-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Gagal Memuat Permintaan Darurat</h3>
            <p id="permintaan-error-message" class="text-xs text-slate-500 mt-1 max-w-md mx-auto"></p>
            <button
                type="button"
                onclick="loadPermintaanList()"
                class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition cursor-pointer"
            >
                Coba Lagi
            </button>
        </div>

        {{-- Empty State --}}
        <div id="permintaan-empty-state" class="hidden py-20 px-6 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Belum ada permintaan darurat</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
                {{ $canManage
                    ? 'Fasilitas kesehatan Anda belum memiliki catatan permintaan darurat. Klik tombol di bawah untuk membuat pengajuan pertama.'
                    : 'Belum ada catatan permintaan darurat yang tercatat dalam sistem.' }}
            </p>
            @if ($canCreate)
                <button
                    type="button"
                    onclick="openCreateModal()"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Permintaan Pertama</span>
                </button>
            @endif
        </div>

        {{-- Data Table --}}
        <div id="permintaan-table-wrapper" class="hidden overflow-x-auto">
            <table class="w-full text-sm" id="tabel-permintaan">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 w-16">ID</th>
                        @if (! $canManage)
                            <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Fasilitas Kesehatan</th>
                        @endif
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Tingkat Urgensi</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Status</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Catatan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Waktu Dibuat</th>
                        @if ($canManage)
                            <th class="text-center px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="permintaan-table-body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ======================================================================= --}}
{{-- MODALS (Hanya dirender untuk peran Faskes yang berwenang)               --}}
{{-- ======================================================================= --}}
@if ($canManage)
    {{-- MODAL: BUAT PERMINTAAN DARURAT --}}
    <div
        id="modal-create-permintaan"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-create-title"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeCreateModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200">
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
                <div>
                    <h2 id="modal-create-title" class="text-base font-bold text-slate-900">Buat Permintaan Darurat</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pengajuan dicatat atas nama {{ auth()->user()->faskes->nama_faskes }}.</p>
                </div>
                <button
                    type="button"
                    onclick="closeCreateModal()"
                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition cursor-pointer"
                    aria-label="Tutup modal"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-create-permintaan" onsubmit="submitCreatePermintaan(event)" class="px-6 py-5 space-y-4">
                <div>
                    <label for="create-urgensi_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tingkat Urgensi <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="create-urgensi_id"
                        name="urgensi_id"
                        required
                        @disabled(! $hasUrgensi)
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition"
                    >
                        <option value="">-- Pilih tingkat urgensi --</option>
                        @foreach ($urgensiList as $urgensi)
                            <option value="{{ $urgensi->id }}">
                                {{ $urgensi->nama_level }} (Batas waktu: {{ $urgensi->batas_waktu_jam }} jam)
                            </option>
                        @endforeach
                    </select>
                    <p id="create-urgensi_id-error" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>

                <div>
                    <label for="create-catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Catatan Tambahan <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea
                        id="create-catatan"
                        name="catatan"
                        rows="3"
                        maxlength="1000"
                        placeholder="Jelaskan kondisi klinis atau konteks kebutuhan darurat..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition resize-none"
                    ></textarea>
                    <p id="create-catatan-error" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>

                <div id="create-form-error" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs"></div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        onclick="closeCreateModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        id="btn-submit-create"
                        @disabled(! $hasUrgensi)
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-semibold shadow-xs transition cursor-pointer"
                    >
                        <span>Kirim Permintaan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: UBAH TINGKAT URGENSI --}}
    <div
        id="modal-edit-permintaan"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-edit-title"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeEditModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200">
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
                <div>
                    <h2 id="modal-edit-title" class="text-base font-bold text-slate-900">Ubah Tingkat Urgensi</h2>
                    <p id="edit-modal-subtitle" class="text-xs text-slate-500 mt-0.5">Perbarui tingkat urgensi permintaan darurat.</p>
                </div>
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition cursor-pointer"
                    aria-label="Tutup modal"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-permintaan" onsubmit="submitEditPermintaan(event)" class="px-6 py-5 space-y-4">
                <input type="hidden" id="edit-permintaan-id" value="">

                <div>
                    <label for="edit-urgensi_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tingkat Urgensi Baru <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="edit-urgensi_id"
                        name="urgensi_id"
                        required
                        @disabled(! $hasUrgensi)
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition"
                    >
                        <option value="">-- Pilih tingkat urgensi --</option>
                        @foreach ($urgensiList as $urgensi)
                            <option value="{{ $urgensi->id }}">
                                {{ $urgensi->nama_level }} (Batas waktu: {{ $urgensi->batas_waktu_jam }} jam)
                            </option>
                        @endforeach
                    </select>
                    <p id="edit-urgensi_id-error" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>

                <div id="edit-form-error" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs"></div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        id="btn-submit-edit"
                        @disabled(! $hasUrgensi)
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-semibold shadow-xs transition cursor-pointer"
                    >
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: KONFIRMASI HAPUS PERMINTAAN --}}
    <div
        id="modal-delete-permintaan"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-delete-title"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeDeleteModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm border border-slate-200">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h2 id="modal-delete-title" class="text-base font-bold text-slate-900">Hapus Permintaan Darurat?</h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Permintaan <strong id="delete-permintaan-label" class="text-slate-800"></strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div id="delete-form-error" class="hidden mt-3 p-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs text-left"></div>
            </div>

            <div class="px-6 pb-6 flex gap-3">
                <input type="hidden" id="delete-permintaan-id" value="">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    id="btn-submit-delete"
                    onclick="submitDeletePermintaan()"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-xs transition cursor-pointer"
                >
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    const PERMINTAAN_API_URL = @json(url('/api/permintaan'));
    const LOGIN_URL = @json(route('login'));
    const CAN_MANAGE = @json($canManage);
    const HAS_URGENSI = @json($hasUrgensi);

    let permintaanCache = [];

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(isoString) {
        if (!isoString) return '-';
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) return escapeHtml(isoString);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function renderStatusBadge(status) {
        const normalized = String(status || '').toLowerCase();
        const styles = {
            diajukan: 'bg-amber-50 text-amber-800 border-amber-200',
            disetujui: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            dikirim: 'bg-sky-50 text-sky-700 border-sky-200',
            diterima: 'bg-teal-50 text-teal-700 border-teal-200',
            dibatalkan: 'bg-red-50 text-red-700 border-red-200',
        };
        const badgeClass = styles[normalized] || 'bg-slate-100 text-slate-700 border-slate-200';
        const label = normalized ? normalized.charAt(0).toUpperCase() + normalized.slice(1) : '-';

        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-xs font-semibold ${badgeClass}">${escapeHtml(label)}</span>`;
    }

    function showBannerAlert(type, message) {
        const container = document.getElementById('api-alert-container');
        const cls = type === 'success'
            ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
            : 'bg-red-50 border-red-200 text-red-800';

        container.className = `p-4 rounded-xl border text-sm flex items-center justify-between ${cls}`;
        container.innerHTML = `
            <span>${escapeHtml(message)}</span>
            <button type="button" onclick="this.parentElement.classList.add('hidden')" class="ml-4 font-bold text-base leading-none opacity-75 hover:opacity-100 cursor-pointer">&times;</button>
        `;
        container.classList.remove('hidden');
    }

    async function apiFetch(url, options = {}) {
        const method = (options.method || 'GET').toUpperCase();
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        };

        if (method !== 'GET' && method !== 'HEAD') {
            headers['Content-Type'] = 'application/json';
            headers['X-CSRF-TOKEN'] = getCsrfToken();
        }

        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options,
            method,
            headers,
        });

        if (response.status === 401) {
            window.location.href = LOGIN_URL;
            throw new Error('Sesi autentikasi Anda telah berakhir. Mengalihkan ke halaman login...');
        }

        let payload = null;
        try {
            payload = await response.json();
        } catch (e) {
            payload = null;
        }

        if (!response.ok) {
            const err = new Error((payload && payload.message) ? payload.message : `Terjadi kesalahan pada server (HTTP ${response.status}).`);
            err.status = response.status;
            err.payload = payload;
            throw err;
        }

        return payload;
    }

    function updateSummaryStats(items) {
        const total = items.length;
        const diajukanCount = items.filter(item => String(item.status || '').toLowerCase() === 'diajukan').length;

        document.getElementById('stat-total-requests').textContent = total;
        document.getElementById('stat-diajukan-requests').textContent = diajukanCount;
        document.getElementById('table-count-badge').textContent = `(${total} permintaan)`;
    }

    function renderPermintaanTable(items) {
        const loadingEl = document.getElementById('permintaan-loading-state');
        const errorEl = document.getElementById('permintaan-error-state');
        const emptyEl = document.getElementById('permintaan-empty-state');
        const tableWrapperEl = document.getElementById('permintaan-table-wrapper');
        const tbody = document.getElementById('permintaan-table-body');

        loadingEl.classList.add('hidden');
        errorEl.classList.add('hidden');
        updateSummaryStats(items);

        if (!items.length) {
            tableWrapperEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            return;
        }

        emptyEl.classList.add('hidden');
        tableWrapperEl.classList.remove('hidden');

        tbody.innerHTML = items.map(item => {
            const isEditable = String(item.status || '').toLowerCase() === 'diajukan';
            const urgensiName = item.urgensi ? item.urgensi.nama_level : `Urgensi #${item.urgensi_id}`;
            const batasJam = item.urgensi && item.urgensi.batas_waktu_jam !== undefined
                ? `<span class="block text-[11px] text-slate-400">Batas waktu: ${escapeHtml(item.urgensi.batas_waktu_jam)} jam</span>`
                : '';

            const faskesCol = !CAN_MANAGE
                ? `<td class="px-6 py-4">
                        <span class="font-semibold text-slate-800 block">${escapeHtml(item.faskes ? item.faskes.nama_faskes : '-')}</span>
                        <span class="text-[11px] text-slate-400">${escapeHtml(item.faskes ? `${item.faskes.jenis} • ${item.faskes.kota}` : '')}</span>
                   </td>`
                : '';

            let actionCol = '';
            if (CAN_MANAGE) {
                if (isEditable) {
                    const editDisabledAttr = !HAS_URGENSI ? 'disabled title="Master urgensi belum tersedia"' : 'title="Ubah tingkat urgensi"';
                    const editBtnClass = HAS_URGENSI
                        ? 'bg-teal-50 hover:bg-teal-100 border-teal-200 text-teal-700 cursor-pointer'
                        : 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed';

                    actionCol = `
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    onclick="openEditModal(${Number(item.id)}, ${Number(item.urgensi_id)})"
                                    ${editDisabledAttr}
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border text-xs font-semibold transition ${editBtnClass}"
                                >
                                    Ubah Urgensi
                                </button>
                                <button
                                    type="button"
                                    onclick="openDeleteModal(${Number(item.id)})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-semibold transition cursor-pointer"
                                    title="Hapus permintaan"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    `;
                } else {
                    actionCol = `
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 text-[11px] font-medium" title="Hanya permintaan berstatus diajukan yang dapat diubah atau dihapus">
                                Terkunci
                            </span>
                        </td>
                    `;
                }
            }

            return `
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 text-xs font-mono font-semibold text-slate-600">#${escapeHtml(item.id)}</td>
                    ${faskesCol}
                    <td class="px-6 py-4">
                        <span class="font-semibold text-slate-800">${escapeHtml(urgensiName)}</span>
                        ${batasJam}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">${renderStatusBadge(item.status)}</td>
                    <td class="px-6 py-4 text-xs sm:text-sm text-slate-600 max-w-xs">
                        <span class="line-clamp-2">${item.catatan ? escapeHtml(item.catatan) : '<span class="text-slate-400">&mdash;</span>'}</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-mono text-slate-500 whitespace-nowrap">${formatDateTime(item.created_at)}</td>
                    ${actionCol}
                </tr>
            `;
        }).join('');
    }

    async function loadPermintaanList() {
        const loadingEl = document.getElementById('permintaan-loading-state');
        const errorEl = document.getElementById('permintaan-error-state');
        const emptyEl = document.getElementById('permintaan-empty-state');
        const tableWrapperEl = document.getElementById('permintaan-table-wrapper');

        loadingEl.classList.remove('hidden');
        errorEl.classList.add('hidden');
        emptyEl.classList.add('hidden');
        tableWrapperEl.classList.add('hidden');

        try {
            const result = await apiFetch(PERMINTAAN_API_URL);
            permintaanCache = Array.isArray(result && result.data) ? result.data : [];
            renderPermintaanTable(permintaanCache);
        } catch (error) {
            loadingEl.classList.add('hidden');
            document.getElementById('permintaan-error-message').textContent = error.message;
            errorEl.classList.remove('hidden');
            document.getElementById('table-count-badge').textContent = '(Gagal memuat)';
        }
    }

    function clearModalErrors(prefix) {
        const generalErr = document.getElementById(`${prefix}-form-error`);
        if (generalErr) {
            generalErr.textContent = '';
            generalErr.classList.add('hidden');
        }
        ['urgensi_id', 'catatan'].forEach(field => {
            const fieldErr = document.getElementById(`${prefix}-${field}-error`);
            if (fieldErr) {
                fieldErr.textContent = '';
                fieldErr.classList.add('hidden');
            }
        });
    }

    function displayValidationErrors(prefix, error) {
        const payload = error.payload || {};
        const fieldErrors = payload.errors || {};
        let hasFieldError = false;

        Object.entries(fieldErrors).forEach(([field, messages]) => {
            const fieldErrEl = document.getElementById(`${prefix}-${field}-error`);
            if (fieldErrEl && Array.isArray(messages) && messages.length > 0) {
                fieldErrEl.textContent = messages[0];
                fieldErrEl.classList.remove('hidden');
                hasFieldError = true;
            }
        });

        if (!hasFieldError) {
            const generalErr = document.getElementById(`${prefix}-form-error`);
            if (generalErr) {
                generalErr.textContent = error.message || 'Terjadi kesalahan saat memproses permintaan.';
                generalErr.classList.remove('hidden');
            }
        }
    }

    function openCreateModal() {
        if (!CAN_MANAGE || !HAS_URGENSI) return;
        clearModalErrors('create');
        document.getElementById('form-create-permintaan').reset();
        document.getElementById('modal-create-permintaan').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        const modal = document.getElementById('modal-create-permintaan');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitCreatePermintaan(event) {
        event.preventDefault();
        if (!CAN_MANAGE || !HAS_URGENSI) return;

        clearModalErrors('create');
        const submitBtn = document.getElementById('btn-submit-create');
        submitBtn.disabled = true;

        const urgensiId = document.getElementById('create-urgensi_id').value;
        const catatanRaw = document.getElementById('create-catatan').value.trim();

        try {
            const result = await apiFetch(PERMINTAAN_API_URL, {
                method: 'POST',
                body: JSON.stringify({
                    urgensi_id: urgensiId ? Number(urgensiId) : null,
                    catatan: catatanRaw !== '' ? catatanRaw : null,
                }),
            });

            closeCreateModal();
            showBannerAlert('success', (result && result.message) || 'Permintaan darurat berhasil dibuat.');
            await loadPermintaanList();
        } catch (error) {
            displayValidationErrors('create', error);
        } finally {
            submitBtn.disabled = !HAS_URGENSI;
        }
    }

    function openEditModal(id, currentUrgensiId) {
        if (!CAN_MANAGE || !HAS_URGENSI) return;
        clearModalErrors('edit');
        document.getElementById('edit-permintaan-id').value = id;
        document.getElementById('edit-modal-subtitle').textContent = `Perbarui tingkat urgensi untuk Permintaan #${id}.`;
        document.getElementById('edit-urgensi_id').value = String(currentUrgensiId || '');
        document.getElementById('modal-edit-permintaan').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('modal-edit-permintaan');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitEditPermintaan(event) {
        event.preventDefault();
        if (!CAN_MANAGE || !HAS_URGENSI) return;

        clearModalErrors('edit');
        const id = document.getElementById('edit-permintaan-id').value;
        const urgensiId = document.getElementById('edit-urgensi_id').value;
        const submitBtn = document.getElementById('btn-submit-edit');
        submitBtn.disabled = true;

        try {
            const result = await apiFetch(`${PERMINTAAN_API_URL}/${encodeURIComponent(id)}`, {
                method: 'PUT',
                body: JSON.stringify({
                    urgensi_id: urgensiId ? Number(urgensiId) : null,
                }),
            });

            closeEditModal();
            showBannerAlert('success', (result && result.message) || 'Tingkat urgensi permintaan berhasil diperbarui.');
            await loadPermintaanList();
        } catch (error) {
            displayValidationErrors('edit', error);
        } finally {
            submitBtn.disabled = !HAS_URGENSI;
        }
    }

    function openDeleteModal(id) {
        if (!CAN_MANAGE) return;
        clearModalErrors('delete');
        document.getElementById('delete-permintaan-id').value = id;
        document.getElementById('delete-permintaan-label').textContent = `#${id}`;
        document.getElementById('modal-delete-permintaan').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modal-delete-permintaan');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitDeletePermintaan() {
        if (!CAN_MANAGE) return;
        clearModalErrors('delete');

        const id = document.getElementById('delete-permintaan-id').value;
        const submitBtn = document.getElementById('btn-submit-delete');
        submitBtn.disabled = true;

        try {
            const result = await apiFetch(`${PERMINTAAN_API_URL}/${encodeURIComponent(id)}`, {
                method: 'DELETE',
            });

            closeDeleteModal();
            showBannerAlert('success', (result && result.message) || 'Permintaan darurat berhasil dihapus.');
            await loadPermintaanList();
        } catch (error) {
            const generalErr = document.getElementById('delete-form-error');
            generalErr.textContent = error.message || 'Gagal menghapus permintaan darurat.';
            generalErr.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeDeleteModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        loadPermintaanList();
    });
</script>
@endpush
@endsection