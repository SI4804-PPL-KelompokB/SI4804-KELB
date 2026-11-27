@extends('layouts.dashboard')

@section('title', 'Kotak Pesan Internal - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-64 bg-gradient-to-l from-teal-50 to-transparent pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>PBI-21</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-slate-500 normal-case font-medium">Manajemen Pesan Internal (Inbox Faskes & Dinkes)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Pesan Internal
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Saluran koordinasi dan komunikasi cepat ketersediaan obat antar faskes, dinkes, dan penyedia.
                </p>
            </div>

            <div class="shrink-0">
                <button
                    type="button"
                    onclick="openComposeModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-sm hover:shadow-md transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tulis Pesan Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== STATS SUMMARY CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('inbox.index', ['folder' => 'inbox']) }}" class="bg-white rounded-xl p-5 border {{ $folder === 'inbox' && empty($status) ? 'border-teal-500 ring-2 ring-teal-500/10' : 'border-slate-200/80 hover:border-slate-300' }} shadow-xs flex items-center gap-4 transition">
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Pesan Masuk</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalInboxCount }}</p>
            </div>
        </a>

        <a href="{{ route('inbox.index', ['folder' => 'inbox', 'status' => 'unread']) }}" class="bg-white rounded-xl p-5 border {{ $status === 'unread' ? 'border-amber-500 ring-2 ring-amber-500/10' : 'border-slate-200/80 hover:border-slate-300' }} shadow-xs flex items-center gap-4 transition">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Belum Dibaca</p>
                <div class="flex items-center gap-2 mt-0.5">
                    <p class="text-2xl font-extrabold text-slate-900">{{ $unreadCount }}</p>
                    @if ($unreadCount > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            Baru
                        </span>
                    @endif
                </div>
            </div>
        </a>

        <a href="{{ route('inbox.index', ['folder' => 'sent']) }}" class="bg-white rounded-xl p-5 border {{ $folder === 'sent' ? 'border-sky-500 ring-2 ring-sky-500/10' : 'border-slate-200/80 hover:border-slate-300' }} shadow-xs flex items-center gap-4 transition">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pesan Terkirim</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalSentCount }}</p>
            </div>
        </a>
    </div>

    {{-- ===== MAIN INBOX CONTAINER ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        {{-- Toolbar: Tabs, Search, & Filters --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Tabs: Kotak Masuk vs Terkirim --}}
            <div class="flex items-center gap-2 border-b md:border-b-0 pb-2 md:pb-0 border-slate-200">
                <a
                    href="{{ route('inbox.index', ['folder' => 'inbox']) }}"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition {{ $folder === 'inbox' ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <span>Kotak Masuk</span>
                    @if ($unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-600 text-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('inbox.index', ['folder' => 'sent']) }}"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition {{ $folder === 'sent' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Pesan Terkirim</span>
                    <span class="text-xs text-slate-400">({{ $totalSentCount }})</span>
                </a>
            </div>

            {{-- Search & Sub-Filter --}}
            <form action="{{ route('inbox.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <input type="hidden" name="folder" value="{{ $folder }}">

                @if ($folder === 'inbox')
                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                    >
                        <option value="" {{ empty($status) ? 'selected' : '' }}>Semua Status</option>
                        <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Belum Dibaca (Unread)</option>
                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Sudah Dibaca (Read)</option>
                    </select>
                @endif

                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari subjek, isi, pengirim..."
                        class="w-full sm:w-64 pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                @if (!empty($search) || !empty($status))
                    <a
                        href="{{ route('inbox.index', ['folder' => $folder]) }}"
                        class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 rounded-xl border border-slate-200 text-center hover:bg-slate-50"
                        title="Reset Filter"
                    >
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table / List View --}}
        @if ($messages->isEmpty())
            <div class="py-16 px-4 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">
                    {{ $folder === 'sent' ? 'Belum ada pesan terkirim' : 'Kotak masuk kosong' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    {{ !empty($search) ? "Tidak ditemukan pesan yang cocok dengan kata kunci \"{$search}\"." : ($folder === 'sent' ? 'Anda belum pernah mengirim pesan kepada faskes atau dinkes lain.' : 'Saat ini belum ada pesan masuk untuk akun Anda.') }}
                </p>
                @if (empty($search))
                    <button
                        type="button"
                        onclick="openComposeModal()"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tulis Pesan Pertama
                    </button>
                @endif
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($messages as $msg)
                    @php
                        $isUnread = ($folder === 'inbox' && !$msg->is_read);
                        $counterpart = ($folder === 'sent') ? $msg->penerima : $msg->pengirim;
                    @endphp
                    <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:bg-slate-50/80 {{ $isUnread ? 'bg-teal-50/40' : 'bg-white' }}">
                        {{-- Left: Indicator + Avatar + Details --}}
                        <div class="flex items-start sm:items-center gap-3.5 flex-1 min-w-0">
                            {{-- Unread Dot --}}
                            @if ($folder === 'inbox')
                                <div class="w-2.5 h-2.5 rounded-full shrink-0 mt-1.5 sm:mt-0 {{ $isUnread ? 'bg-teal-500 shadow-xs shadow-teal-500/50' : 'bg-transparent' }}"></div>
                            @endif

                            {{-- Avatar --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isUnread ? 'bg-teal-100 text-teal-800 ring-2 ring-teal-500/20' : 'bg-slate-100 text-slate-700' }}">
                                {{ strtoupper(substr($counterpart?->name ?? '?', 0, 2)) }}
                            </div>

                            {{-- Message Content Preview --}}
                            <div class="min-w-0 flex-1 cursor-pointer" onclick="viewMessageDetail({{ $msg->id }})">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="text-xs font-bold text-slate-900 {{ $isUnread ? 'text-teal-950 font-extrabold' : '' }}">
                                        {{ $counterpart?->name ?? 'Pengguna Tidak Diketahui' }}
                                    </span>

                                    @if ($counterpart?->faskes)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $counterpart->faskes->nama_faskes }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $counterpart?->role_label }}
                                        </span>
                                    @endif

                                    @if ($folder === 'sent')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $msg->is_read ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $msg->is_read ? '✓✓ Dibaca' : '✓ Terkirim' }}
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-xs sm:text-sm font-semibold truncate {{ $isUnread ? 'text-slate-900 font-bold' : 'text-slate-700' }}">
                                    {{ $msg->subjek }}
                                </h4>
                                <p class="text-xs text-slate-500 truncate mt-0.5">
                                    {{ Str::limit($msg->isi, 90) }}
                                </p>
                            </div>
                        </div>

                        {{-- Right: Date + Actions --}}
                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <span class="text-[11px] text-slate-400 font-mono whitespace-nowrap">
                                {{ $msg->created_at->translatedFormat('d M Y, H:i') }}
                            </span>

                            <div class="flex items-center gap-1">
                                {{-- Tombol Baca Detail --}}
                                <button
                                    type="button"
                                    onclick="viewMessageDetail({{ $msg->id }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-teal-600 hover:bg-teal-50 transition"
                                    title="Baca Pesan Lengkap"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- Tombol Toggle Read/Unread (Hanya untuk Kotak Masuk) --}}
                                @if ($folder === 'inbox')
                                    <form action="{{ route('inbox.update', $msg->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_read" value="{{ $msg->is_read ? 0 : 1 }}">
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                            title="{{ $msg->is_read ? 'Tandai Belum Dibaca' : 'Tandai Sudah Dibaca' }}"
                                        >
                                            @if ($msg->is_read)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Hapus Pesan --}}
                                <form action="{{ route('inbox.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini dari kotak pesan Anda?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                                        title="Hapus Pesan"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            @if ($messages->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $messages->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

{{-- ===== MODAL 1: TULIS PESAN BARU (COMPOSE) ===== --}}
<div id="modalCompose" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        {{-- Modal Header --}}
        <div class="px-6 py-4.5 bg-gradient-to-r from-teal-600 to-emerald-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold tracking-tight">Tulis Pesan Internal Baru</h3>
                    <p class="text-[11px] text-teal-100">Kirim koordinasi resmi ketersediaan obat</p>
                </div>
            </div>
            <button type="button" onclick="closeComposeModal()" class="text-white/80 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
        </div>

        {{-- Modal Form --}}
        <form action="{{ route('inbox.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Penerima --}}
            <div>
                <label for="penerima_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Penerima Pesan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select
                        name="penerima_id"
                        id="penerima_id"
                        required
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 bg-white"
                    >
                        <option value="">-- Pilih Tujuan Pengguna / Instansi --</option>
                        @foreach ($recipients as $recipient)
                            <option value="{{ $recipient->id }}" {{ old('penerima_id') == $recipient->id ? 'selected' : '' }}>
                                {{ $recipient->name }} ({{ $recipient->role_label }}{{ $recipient->faskes ? ' - ' . $recipient->faskes->nama_faskes : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('penerima_id')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subjek Pesan --}}
            <div>
                <label for="subjek" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Subjek Koordinasi <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="subjek"
                    id="subjek"
                    value="{{ old('subjek') }}"
                    required
                    maxlength="200"
                    placeholder="Contoh: Permintaan Konfirmasi Ketersediaan Serum Anti Bisa Ular"
                    class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800"
                >
                @error('subjek')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Isi Pesan --}}
            <div>
                <label for="isi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Isi Pesan <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="isi"
                    id="isi"
                    rows="5"
                    required
                    maxlength="5000"
                    placeholder="Tuliskan detail koordinasi, nama obat, jumlah yang dibutuhkan, atau konfirmasi ketersediaan..."
                    class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 resize-y"
                >{{ old('isi') }}</textarea>
                @error('isi')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
                <button
                    type="button"
                    onclick="closeComposeModal()"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl border border-slate-200 hover:bg-slate-50 transition"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-xs transition flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Pesan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL 2: BACA DETAIL PESAN (READ MESSAGE) ===== --}}
<div id="modalDetail" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        {{-- Modal Detail Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/70 flex items-start justify-between gap-4">
            <div class="space-y-1">
                <span id="detailBadge" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800">
                    Pesan Masuk
                </span>
                <h3 id="detailSubjek" class="text-base font-extrabold text-slate-900 leading-snug">
                    Memuat subjek...
                </h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        {{-- Metadata Pengirim & Penerima --}}
        <div class="px-6 py-4 bg-white border-b border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Dari (Pengirim):</span>
                <p id="detailPengirim" class="font-bold text-slate-800 mt-0.5">-</p>
                <p id="detailPengirimInstansi" class="text-slate-500 text-[11px]">-</p>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Kepada (Tujuan):</span>
                <p id="detailPenerima" class="font-bold text-slate-800 mt-0.5">-</p>
                <p id="detailPenerimaInstansi" class="text-slate-500 text-[11px]">-</p>
            </div>
            <div class="sm:col-span-2 pt-1 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Waktu Dikirim: <strong id="detailWaktu" class="text-slate-700 font-mono">-</strong></span>
                <span id="detailStatusBaca">-</span>
            </div>
        </div>

        {{-- Isi Pesan --}}
        <div class="p-6 bg-slate-50/30">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Isi Pesan Koordinasi:</span>
            <div id="detailIsi" class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs font-sans min-h-[120px]">
                Memuat pesan...
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-between gap-2">
            <button
                type="button"
                id="btnReply"
                onclick="replyFromDetail()"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 rounded-xl transition"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                <span>Balas Pesan</span>
            </button>

            <button
                type="button"
                onclick="closeDetailModal()"
                class="px-5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    let currentDetailMessage = null;

    function openComposeModal(penerimaId = null, defaultSubjek = '') {
        if (penerimaId) {
            document.getElementById('penerima_id').value = penerimaId;
        }
        if (defaultSubjek) {
            document.getElementById('subjek').value = defaultSubjek;
        }
        document.getElementById('modalCompose').classList.remove('hidden');
    }

    function closeComposeModal() {
        document.getElementById('modalCompose').classList.add('hidden');
    }

    function closeDetailModal() {
        document.getElementById('modalDetail').classList.add('hidden');
    }

    function viewMessageDetail(id) {
        // Tampilkan modal dan state loading
        const modal = document.getElementById('modalDetail');
        modal.classList.remove('hidden');
        document.getElementById('detailSubjek').innerText = 'Memuat pesan...';
        document.getElementById('detailIsi').innerText = 'Sedang mengambil detail dari server...';

        fetch(`/api/inbox/${id}`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const data = response.data;
                    currentDetailMessage = data;

                    document.getElementById('detailSubjek').innerText = data.subjek;
                    document.getElementById('detailIsi').innerText = data.isi;

                    const pengirimName = data.pengirim ? data.pengirim.name : 'Tidak Diketahui';
                    const pengirimFaskes = (data.pengirim && data.pengirim.faskes) ? data.pengirim.faskes.nama_faskes : (data.pengirim ? data.pengirim.role : '');
                    document.getElementById('detailPengirim').innerText = pengirimName;
                    document.getElementById('detailPengirimInstansi').innerText = pengirimFaskes;

                    const penerimaName = data.penerima ? data.penerima.name : 'Tidak Diketahui';
                    const penerimaFaskes = (data.penerima && data.penerima.faskes) ? data.penerima.faskes.nama_faskes : (data.penerima ? data.penerima.role : '');
                    document.getElementById('detailPenerima').innerText = penerimaName;
                    document.getElementById('detailPenerimaInstansi').innerText = penerimaFaskes;

                    const dateObj = new Date(data.created_at);
                    document.getElementById('detailWaktu').innerText = dateObj.toLocaleString('id-ID', {
                        day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    document.getElementById('detailStatusBaca').innerHTML = data.is_read
                        ? '<span class="text-emerald-600 font-semibold">✓ Sudah Dibaca</span>'
                        : '<span class="text-amber-600 font-semibold">• Belum Dibaca</span>';

                    // Update tombol balas
                    const currentUserId = {{ auth()->id() }};
                    const btnReply = document.getElementById('btnReply');
                    if (data.pengirim_id === currentUserId) {
                        btnReply.classList.add('hidden');
                    } else {
                        btnReply.classList.remove('hidden');
                    }
                } else {
                    alert(response.message || 'Gagal memuat pesan.');
                    closeDetailModal();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat memuat pesan.');
                closeDetailModal();
            });
    }

    function replyFromDetail() {
        if (!currentDetailMessage) return;

        const senderId = currentDetailMessage.pengirim_id;
        const subjek = currentDetailMessage.subjek.startsWith('Re: ')
            ? currentDetailMessage.subjek
            : 'Re: ' + currentDetailMessage.subjek;

        closeDetailModal();
        openComposeModal(senderId, subjek);
    }

    // Tutup modal jika klik di area backdrop luar
    window.addEventListener('click', function(e) {
        const modalCompose = document.getElementById('modalCompose');
        const modalDetail = document.getElementById('modalDetail');
        if (e.target === modalCompose) closeComposeModal();
        if (e.target === modalDetail) closeDetailModal();
    });
</script>
@endsection
