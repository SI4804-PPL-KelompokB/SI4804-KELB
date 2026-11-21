@extends('layouts.dashboard')

@section('title', 'Dashboard Admin - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Top Greeting Banner -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-72 bg-gradient-to-l from-teal-50/80 to-transparent pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">
                    <span>MedStock.id Core</span>
                    <span>&bull;</span>
                    <span class="text-slate-400">Pusat Administrasi &amp; Pengaturan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Anda masuk sebagai <span class="font-semibold text-teal-700">Administrator Sistem (Admin)</span> dengan akses tata kelola master data.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a
                    href="{{ route('wilayah.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Kelola Manajemen Wilayah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Operational Module Hub -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a
            href="{{ route('wilayah.index') }}"
            class="group bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-teal-300 shadow-2xs hover:shadow-sm transition flex items-start gap-4"
        >
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white flex items-center justify-center shrink-0 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-teal-700 transition">Manajemen Wilayah</h2>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-semibold">Aktif</span>
                </div>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Kelola master data zona geografis fasilitas kesehatan di wilayah Kota Bandung.
                </p>
            </div>
        </a>

        <div class="bg-slate-50/80 rounded-2xl p-6 border border-dashed border-slate-200 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-700">Modul Administrasi Lanjutan</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Manajemen pengguna, fasilitas kesehatan, dan master tingkat urgensi (PBI-11) akan muncul di navigasi setelah diimplementasikan.
                </p>
            </div>
        </div>
    </div>

    <!-- Informasi Status Autentikasi Pengguna & Sesi Aktif -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Informasi Akun Terautentikasi</h3>
            <div class="space-y-3 text-xs sm:text-sm">
                <div>
                    <span class="text-slate-400 block text-[11px]">Nama Pengguna:</span>
                    <span class="font-bold text-slate-800">{{ $user->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Email Login:</span>
                    <span class="font-mono text-slate-700">{{ $user->email }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Peran (Role):</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 font-bold border border-teal-200 text-xs">
                        {{ $user->role_label }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Jabatan:</span>
                    <span class="text-slate-700">{{ $user->jabatan ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Hak Istimewa Admin</h3>
            <div class="space-y-3 text-xs sm:text-sm">
                <div>
                    <span class="text-slate-400 block text-[11px]">Cakupan Wewenang:</span>
                    <span class="font-bold text-slate-800">Super Administrator Sistem</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Koneksi Entitas:</span>
                    <span class="text-slate-700">Akses Tanpa Batas Wilayah / Global</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status Akses:</span>
                    <span class="text-slate-700">Tingkat Hak Akses Tertinggi</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Sesi &amp; Keamanan (NF1)</h3>
            <div class="space-y-3 text-xs sm:text-sm">
                <div>
                    <span class="text-slate-400 block text-[11px]">Waktu Login Terakhir:</span>
                    <span class="text-slate-700 font-mono">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'Sesi Baru' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status Akun:</span>
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Aktif &amp; Terverifikasi
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Proteksi Hak Akses:</span>
                    <span class="text-slate-600">Akses khusus Admin Sistem</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection