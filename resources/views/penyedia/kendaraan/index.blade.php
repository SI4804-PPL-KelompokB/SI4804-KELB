@extends('layouts.dashboard')

@section('title', 'Data Kendaraan / Kurir Internal - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600 mb-1">
                    <span>{{ $user->faskes->nama_faskes ?? 'Gudang Farmasi Penyedia' }}</span>
                    <span>•</span>
                    <span class="text-slate-400">PBI-14 Data Kendaraan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Data Kendaraan / Kurir Internal
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Kelola armada pengiriman obat darurat milik fasilitas Anda.
                </p>
            </div>

            {{-- Tombol Tambah Kendaraan --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('penyedia.kendaraan.create') }}"
                   class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Kendaraan</span>
                </a>
                <a href="{{ route('penyedia.dashboard') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Total Kendaraan</p>
                    <p class="text-xl font-extrabold text-slate-900">{{ $kendaraan->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Aktif</p>
                    <p class="text-xl font-extrabold text-emerald-700">{{ $kendaraan->where('status', 'aktif')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Maintenance</p>
                    <p class="text-xl font-extrabold text-amber-700">{{ $kendaraan->where('status', 'maintenance')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Nonaktif</p>
                    <p class="text-xl font-extrabold text-red-700">{{ $kendaraan->where('status', 'nonaktif')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Data Kendaraan --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Daftar Kendaraan / Kurir Internal
            </h3>
        </div>

        @if($kendaraan->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-700 mb-1">Belum ada data kendaraan</h4>
                <p class="text-xs text-slate-400 mb-4">Tambahkan kendaraan/kurir pertama untuk mulai mengelola armada pengiriman.</p>
                <a href="{{ route('penyedia.kendaraan.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kendaraan Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">No</th>
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Nama Kurir</th>
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Jenis Kendaraan</th>
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Plat Nomor</th>
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">No. Telepon</th>
                            <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Status</th>
                            <th class="text-center px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kendaraan as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-3.5 text-slate-500 font-mono">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div>
                                        <span class="font-bold text-slate-800">{{ $item->nama_kurir }}</span>
                                        @if($item->keterangan)
                                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ Str::limit($item->keterangan, 40) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 text-[11px] font-semibold border border-sky-200">
                                        @if($item->jenis_kendaraan === 'Ambulans')
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        @elseif($item->jenis_kendaraan === 'Motor')
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                        @endif
                                        {{ $item->jenis_kendaraan }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono font-bold text-slate-700">{{ $item->plat_nomor }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $item->nomor_telepon ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    @if($item->status === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @elseif($item->status === 'maintenance')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-semibold border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Maintenance
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-[11px] font-semibold border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('penyedia.kendaraan.edit', $item) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-[11px] font-semibold border border-sky-200 transition"
                                           title="Edit data kendaraan">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('penyedia.kendaraan.destroy', $item) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data kendaraan/kurir \'{{ $item->nama_kurir }}\'?')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-[11px] font-semibold border border-red-200 transition cursor-pointer"
                                                    title="Hapus data kendaraan">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
