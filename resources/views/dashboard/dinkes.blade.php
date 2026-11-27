@extends('layouts.dashboard')

@section('title', 'Dashboard Dinkes - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Top Greeting Banner -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-72 bg-gradient-to-l from-emerald-50/90 to-transparent pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600 mb-1">
                    <span>Dinas Kesehatan Kota Bandung</span>
                    <span>&bull;</span>
                    <span class="text-slate-400">Pusat Koordinasi Wilayah</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pusat koordinasi <span class="font-semibold text-emerald-700">Dinas Kesehatan (Dinkes)</span> untuk pengelolaan wilayah dan pengawasan distribusi obat.
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
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-teal-600 group-hover:text-white flex items-center justify-center shrink-0 transition">
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
                    Atur pembagian wilayah dan zona fasilitas kesehatan untuk mendukung koordinasi distribusi darurat.
                </p>
            </div>
        </a>

        <div class="bg-slate-50/80 rounded-2xl p-6 border border-dashed border-slate-200 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-700">Koordinasi &amp; Rekomendasi Distribusi</h2>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Antarmuka pemantauan permintaan lintas faskes dan mesin rekomendasi distribusi (PBI-12) akan ditambahkan pada tahap berikutnya.
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
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 text-xs">
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
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Karakteristik Otoritas Dinkes</h3>
            <div class="space-y-3 text-xs sm:text-sm">
                <div>
                    <span class="text-slate-400 block text-[11px]">Kategori Instansi:</span>
                    <span class="font-bold text-slate-800">Dinas Kesehatan Daerah</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Cakupan Relasi:</span>
                    <span class="text-slate-700">Tidak terikat faskes tunggal (Otoritas Lintas Wilayah)</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Wilayah Operasional:</span>
                    <span class="text-slate-700">Kota Bandung</span>
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
                    <span class="text-slate-600">Hanya dapat mengakses area Dinas Kesehatan</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection