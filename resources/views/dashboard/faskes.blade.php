@extends('layouts.dashboard')

@section('title', 'Dashboard Faskes - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Top Greeting Banner (Sesuai Mockup 4.6) -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">
                    <span>{{ $user->faskes->nama_faskes ?? 'Fasilitas Kesehatan' }}</span>
                    <span>•</span>
                    <span class="text-slate-400">Kota Bandung</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Anda berhasil masuk melalui otentikasi peran <span class="font-semibold text-teal-700">Fasilitas Kesehatan (Faskes)</span>.
                </p>
            </div>
        </div>
    </div>

    <!-- Informasi Status Autentikasi Pengguna & Sesi Aktif -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kartu 1: Profil & Akun -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
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
                    <span class="text-slate-400 block text-[11px]">Jabatan / Penugasan:</span>
                    <span class="text-slate-700">{{ $user->jabatan ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Fasilitas Kesehatan Terhubung -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Profil Fasilitas Terkait</h3>
            @if($user->faskes)
                <div class="space-y-3 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Faskes:</span>
                        <span class="font-bold text-slate-800">{{ $user->faskes->nama_faskes }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Jenis Fasilitas:</span>
                        <span class="text-slate-700">{{ $user->faskes->jenis }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Alamat:</span>
                        <span class="text-slate-700">{{ $user->faskes->alamat }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Wilayah:</span>
                        <span class="text-slate-700">{{ $user->faskes->kota }}</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400">Tidak terikat pada entitas faskes spesifik.</p>
            @endif
        </div>

        <!-- Kartu 3: Status Sesi & Keamanan -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Sesi & Keamanan (NF1)</h3>
            <div class="space-y-3 text-xs sm:text-sm">
                <div>
                    <span class="text-slate-400 block text-[11px]">Waktu Login Terakhir:</span>
                    <span class="text-slate-700 font-mono">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'Sesi Baru' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status Akun:</span>
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Aktif & Terverifikasi
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Proteksi Hak Akses:</span>
                    <span class="text-slate-600">Hanya dapat mengakses area Faskes</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
