@extends('layouts.dashboard')

@section('title', 'Dashboard Dinkes - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Top Greeting Banner -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600 mb-1">
                    <span>Dinas Kesehatan Kota Bandung</span>
                    <span>•</span>
                    <span class="text-slate-400">Pusat Koordinasi Wilayah</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Anda berhasil masuk melalui otentikasi peran <span class="font-semibold text-emerald-700">Dinas Kesehatan (Dinkes)</span>.
                </p>
            </div>

            <!-- Tombol Keluar Langsung di Banner -->
            <div class="flex items-center gap-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                        class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Uji Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Informasi Status Autentikasi Pengguna & Sesi Aktif -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
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
                    <span class="text-slate-600">Hanya dapat mengakses area Dinas Kesehatan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Penjelasan Kepatuhan Instruksi -->
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 text-slate-700">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Catatan Implementasi Fitur Sesuai Permintaan</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Sesuai instruksi spesifik Anda: <em>"saya hanya ingin anda untuk membuat fitur login dan logout tanpa mengerjakan apapun yang lainnya"</em>, sistem hanya memproses autentikasi <strong>Login</strong> dan <strong>Logout</strong>. Seluruh modul lain (PBI-01 s/d PBI-21) sengaja tidak dikerjakan sesuai batasan ruang lingkup yang Anda tetapkan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
