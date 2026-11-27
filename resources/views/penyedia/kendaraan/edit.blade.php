@extends('layouts.dashboard')

@section('title', 'Edit Kendaraan / Kurir - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600 mb-1">
                    <span>{{ $user->faskes->nama_faskes ?? 'Gudang Farmasi Penyedia' }}</span>
                    <span>•</span>
                    <span class="text-slate-400">PBI-14 Edit Data</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Edit Data Kendaraan / Kurir
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Perbarui informasi kendaraan <span class="font-semibold text-sky-700">{{ $kendaraan->nama_kurir }}</span> — {{ $kendaraan->plat_nomor }}.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('penyedia.kendaraan.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Form Edit Kendaraan --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Formulir Edit Kendaraan / Kurir
            </h3>
        </div>

        <form action="{{ route('penyedia.kendaraan.update', $kendaraan) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Kurir --}}
                <div>
                    <label for="nama_kurir" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Kurir / Pengemudi <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama_kurir"
                        name="nama_kurir"
                        value="{{ old('nama_kurir', $kendaraan->nama_kurir) }}"
                        required
                        placeholder="Contoh: Ahmad Suryadi"
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border {{ $errors->has('nama_kurir') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-sky-500' }} focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800 transition"
                    >
                    @error('nama_kurir')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Jenis Kendaraan --}}
                <div>
                    <label for="jenis_kendaraan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jenis Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select
                            id="jenis_kendaraan"
                            name="jenis_kendaraan"
                            required
                            class="w-full px-3.5 py-2.5 pr-10 text-xs sm:text-sm rounded-xl border {{ $errors->has('jenis_kendaraan') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-sky-500' }} focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800 bg-white transition appearance-none"
                        >
                            <option value="">— Pilih Jenis Kendaraan —</option>
                            @foreach($jenisKendaraan as $jenis)
                                <option value="{{ $jenis }}" {{ old('jenis_kendaraan', $kendaraan->jenis_kendaraan) == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('jenis_kendaraan')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Plat Nomor --}}
                <div>
                    <label for="plat_nomor" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Plat Nomor Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="plat_nomor"
                        name="plat_nomor"
                        value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}"
                        required
                        placeholder="Contoh: D 1234 ABC"
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border {{ $errors->has('plat_nomor') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-sky-500' }} focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800 font-mono transition"
                    >
                    @error('plat_nomor')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label for="nomor_telepon" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Telepon Kurir
                    </label>
                    <input
                        type="text"
                        id="nomor_telepon"
                        name="nomor_telepon"
                        value="{{ old('nomor_telepon', $kendaraan->nomor_telepon) }}"
                        placeholder="Contoh: 0812-3456-7890"
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border {{ $errors->has('nomor_telepon') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-sky-500' }} focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800 transition"
                    >
                    @error('nomor_telepon')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Status Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full px-3.5 py-2.5 pr-10 text-xs sm:text-sm rounded-xl border {{ $errors->has('status') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-sky-500' }} focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800 bg-white transition appearance-none"
                        >
                            <option value="aktif" {{ old('status', $kendaraan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $kendaraan->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="maintenance" {{ old('status', $kendaraan->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('status')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Keterangan Tambahan
                    </label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        placeholder="Catatan opsional tentang kendaraan atau kurir ini..."
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border {{ $errors->has('keterangan') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-sky-500' }} focus:outline-none focus:ring-2 focus:ring-sky-500/20 text-slate-800 transition resize-none"
                    >{{ old('keterangan', $kendaraan->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Info Metadata --}}
            <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase tracking-wider font-semibold">ID Kendaraan</span>
                        <span class="font-mono font-bold text-slate-700">#{{ $kendaraan->id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase tracking-wider font-semibold">Didaftarkan</span>
                        <span class="text-slate-700">{{ $kendaraan->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase tracking-wider font-semibold">Terakhir Diubah</span>
                        <span class="text-slate-700">{{ $kendaraan->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase tracking-wider font-semibold">Fasilitas</span>
                        <span class="text-slate-700">{{ $kendaraan->faskes->nama_faskes ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-between gap-3 mt-8 pt-6 border-t border-slate-100">
                {{-- Tombol Hapus (Memicu form terpisah di luar agar tidak nested form) --}}
                <button type="button"
                        onclick="if(confirm('Apakah Anda yakin ingin menghapus data kendaraan/kurir \'{{ $kendaraan->nama_kurir }}\'? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('form-delete-kendaraan').submit(); }"
                        class="px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs sm:text-sm font-semibold border border-red-200 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Kendaraan
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('penyedia.kendaraan.index') }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold border border-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white text-xs sm:text-sm font-semibold shadow-md shadow-sky-600/20 hover:shadow-lg transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Perbarui Data Kendaraan
                    </button>
                </div>
            </div>
        </form>

        {{-- Form Hapus Terpisah (Diluar form edit untuk mencegah nested form) --}}
        <form id="form-delete-kendaraan" action="{{ route('penyedia.kendaraan.destroy', $kendaraan) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
