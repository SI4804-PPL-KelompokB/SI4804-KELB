@extends('layouts.dashboard')

@section('title', 'Dashboard Penyedia - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Top Greeting Banner -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600 mb-1">
                    <span>{{ $user->faskes->nama_faskes ?? 'Gudang Farmasi Penyedia' }}</span>
                    <span>•</span>
                    <span class="text-slate-400">Penyedia Terdaftar</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Anda berhasil masuk melalui otentikasi peran <span class="font-semibold text-sky-700">Penyedia Obat</span>.
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
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 font-bold border border-sky-200 text-xs">
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
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Fasilitas Gudang / Penyuplai</h3>
            @if($user->faskes)
                <div class="space-y-3 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Entitas:</span>
                        <span class="font-bold text-slate-800">{{ $user->faskes->nama_faskes }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Jenis:</span>
                        <span class="text-slate-700">{{ $user->faskes->jenis }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Alamat Gudang:</span>
                        <span class="text-slate-700">{{ $user->faskes->alamat }}</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400">Penyedia Mandiri</p>
            @endif
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
                    <span class="text-slate-600">Hanya dapat mengakses area Penyedia Obat</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Fitur Penyedia Obat -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Menu Fitur Penyedia Obat
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Kartu Menu: Kelola Kendaraan / Kurir --}}
                <a href="{{ route('penyedia.kendaraan.index') }}"
                   class="group p-5 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 group-hover:bg-sky-200 transition">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-800 transition">Data Kendaraan / Kurir</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                Kelola armada pengiriman: tambah, lihat, edit plat nomor, dan hapus kurir/ambulans.
                            </p>
                            <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-sky-600 uppercase tracking-wider">
                                PBI-14
                                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>

                {{-- Placeholder fitur lain yang belum aktif --}}
                <div class="p-5 rounded-xl border border-slate-100 bg-slate-50/50 opacity-60">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-500">Stok Obat Gudang</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Fitur belum tersedia pada sprint ini.</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-xl border border-slate-100 bg-slate-50/50 opacity-60">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-500">Riwayat Distribusi</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Fitur belum tersedia pada sprint ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
