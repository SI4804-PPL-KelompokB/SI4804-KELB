@extends('layouts.auth')

@section('title', 'Masuk - MedStock.id')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    <!-- PANEL KIRI: Identitas Visual & Pesan Singkat Sistem (Sesuai Mockup 4.5) -->
    <div class="lg:w-1/2 bg-gradient-to-br from-[#0c2635] via-[#0d2e3f] to-[#071b26] text-white p-8 lg:p-14 flex flex-col justify-between relative overflow-hidden">
        <!-- Dekorasi Background Glow & Grid Lines -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#1f4b63_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>

        <!-- Header Panel Kiri: Logo & Badge -->
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                    <!-- Icon Medis MedStock -->
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <span class="text-2xl font-bold tracking-tight text-white flex items-center gap-1">
                        MedStock<span class="text-teal-400">.id</span>
                    </span>
                    <span class="text-[10px] text-teal-300/80 block uppercase tracking-wider font-semibold">Distribusi Obat Darurat</span>
                </div>
            </div>

            <!-- Tagline Pill -->
            <div class="mt-8 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/25 text-teal-300 text-xs font-semibold tracking-wider uppercase">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Terhubung untuk Keselamatan
            </div>

            <!-- Hero Title & Subtitle Mockup 4.5 -->
            <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Obat tepat waktu.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-200 via-emerald-200 to-white">
                    Pelayanan tetap terjaga.
                </span>
            </h1>
            <p class="mt-4 text-slate-300 text-sm sm:text-base leading-relaxed max-w-lg">
                Koordinasi kebutuhan darurat dan surplus obat antar fasilitas kesehatan, dalam satu jaringan tepercaya.
            </p>
        </div>

        <!-- Visual Ilustrasi Isometrik Rumah Sakit & Jaringan Medis -->
        <div class="my-8 py-4 relative z-10 flex items-center justify-center">
            <div class="w-full max-w-md bg-[#13374b]/60 border border-[#214f6b] rounded-2xl p-6 backdrop-blur-md shadow-2xl relative">
                <!-- SVG Ilustrasi Jaringan Fasilitas Medis -->
                <svg viewBox="0 0 400 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-md">
                    <!-- Ground Grid -->
                    <ellipse cx="200" cy="180" rx="170" ry="50" fill="#0c2331" opacity="0.6"/>
                    <ellipse cx="200" cy="180" rx="140" ry="38" stroke="#1c4b66" stroke-width="1.5" stroke-dasharray="4 4"/>
                    
                    <!-- Connection Lines -->
                    <path d="M110 145 L200 110 L290 145" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-dasharray="6 4" opacity="0.8"/>
                    <path d="M200 110 L200 185" stroke="#10b981" stroke-width="2" stroke-linecap="round" opacity="0.7"/>

                    <!-- Building 1: Puskesmas / Faskes (Kiri) -->
                    <g transform="translate(70, 100)">
                        <path d="M40 0 L75 20 L40 40 L5 20 Z" fill="#2dd4bf" opacity="0.9"/>
                        <path d="M5 20 L40 40 L40 75 L5 55 Z" fill="#0f766e"/>
                        <path d="M40 40 L75 20 L75 55 L40 75 Z" fill="#115e59"/>
                        <!-- Medical cross badge -->
                        <circle cx="40" cy="20" r="12" fill="#ffffff"/>
                        <path d="M40 13 v14 M33 20 h14" stroke="#0f766e" stroke-width="3" stroke-linecap="round"/>
                        <text x="40" y="90" fill="#99f6e4" font-size="9" text-anchor="middle" font-weight="600">Puskesmas Peminta</text>
                    </g>

                    <!-- Building 2: Dinas Kesehatan Hub (Tengah / Atas) -->
                    <g transform="translate(160, 40)">
                        <path d="M40 0 L80 22 L40 44 L0 22 Z" fill="#34d399" opacity="0.9"/>
                        <path d="M0 22 L40 44 L40 90 L0 68 Z" fill="#059669"/>
                        <path d="M40 44 L80 22 L80 68 L40 90 Z" fill="#047857"/>
                        <!-- Windows -->
                        <path d="M12 40 L30 50 L30 65 L12 55 Z" fill="#6ee7b7" opacity="0.8"/>
                        <path d="M50 50 L68 40 L68 55 L50 65 Z" fill="#6ee7b7" opacity="0.8"/>
                        <circle cx="40" cy="22" r="14" fill="#ffffff"/>
                        <!-- Shield emblem -->
                        <path d="M35 17 h10 v6 a5 5 0 0 1 -10 0 z" fill="#059669"/>
                        <text x="40" y="105" fill="#a7f3d0" font-size="10" text-anchor="middle" font-weight="700">Dinas Kesehatan</text>
                    </g>

                    <!-- Building 3: Gudang Farmasi / Penyedia (Kanan) -->
                    <g transform="translate(250, 100)">
                        <path d="M40 0 L75 20 L40 40 L5 20 Z" fill="#38bdf8" opacity="0.9"/>
                        <path d="M5 20 L40 40 L40 75 L5 55 Z" fill="#0284c7"/>
                        <path d="M40 40 L75 20 L75 55 L40 75 Z" fill="#0369a1"/>
                        <circle cx="40" cy="20" r="12" fill="#ffffff"/>
                        <!-- Box icon -->
                        <path d="M34 16 L40 13 L46 16 L40 19 Z M34 16 L40 19 L40 25 L34 22 Z M46 16 L40 19 L40 25 L46 22 Z" fill="#0284c7"/>
                        <text x="40" y="90" fill="#bae6fd" font-size="9" text-anchor="middle" font-weight="600">Gudang Penyedia</text>
                    </g>

                    <!-- Pulse Wave and Nodes -->
                    <circle cx="200" cy="180" r="5" fill="#10b981"/>
                    <circle cx="200" cy="180" r="12" stroke="#10b981" stroke-width="1.5" opacity="0.5"/>
                    <text x="200" y="202" fill="#94a3b8" font-size="9" text-anchor="middle">Sistem Distribusi Cepat & Realtime</text>
                </svg>

                <div class="mt-3 flex items-center justify-between text-xs text-slate-300 border-t border-slate-700/60 pt-3">
                    <span class="flex items-center gap-1.5 text-teal-300 font-medium">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        Status Sistem: Aktif
                    </span>
                    <span class="text-slate-400">Kota Bandung • 2026</span>
                </div>
            </div>
        </div>

        <!-- Footer Panel Kiri Mockup 4.5 -->
        <div class="relative z-10 border-t border-slate-800/80 pt-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-teal-500/10 border border-teal-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-white">Satu jaringan. Saling menguatkan.</p>
                <p class="text-[11px] text-slate-400">Terkoordinasi bersama Dinas Kesehatan Kota Bandung</p>
            </div>
        </div>
    </div>

    <!-- PANEL KANAN: Formulir Login (Sesuai Mockup 4.5) -->
    <div class="lg:w-1/2 bg-white flex flex-col justify-between p-8 sm:p-12 lg:p-16">
        <!-- Top Navigation -->
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span class="font-medium text-slate-400">MedStock.id Portal</span>
            <div class="flex items-center gap-4">
                <a href="#quickhelp" onclick="alert('Pusat Bantuan MedStock: Hubungi helpdesk@medstock.id atau 022-2031122 untuk registrasi faskes baru.')" class="hover:text-teal-600 transition">Pusat Bantuan</a>
                <span class="text-slate-300">•</span>
                <a href="#info" onclick="alert('MedStock.id versi 1.0 - Sistem Distribusi Obat Darurat Kota Bandung')" class="hover:text-teal-600 transition">Bantuan</a>
            </div>
        </div>

        <!-- Form Box Utama -->
        <div class="w-full max-w-md mx-auto my-auto py-6">
            <!-- Badge Akses Khusus -->
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-semibold mb-3 border border-emerald-100">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Akses khusus mitra kesehatan
            </div>

            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Selamat datang kembali
            </h2>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-500">
                Masuk untuk menjaga ketersediaan obat di fasilitas Anda.
            </p>

            <!-- Flash Alert: Pesan Status Berhasil Logout / Error -->
            @if (session('status'))
                <div class="mt-4 p-3.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-xs flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mt-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login.attempt') }}" method="POST" class="mt-6 space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', 'ayu.nuraini@puskesmas.go.id') }}"
                            required
                            autocomplete="email"
                            placeholder="ayu.nuraini@puskesmas.go.id"
                            class="w-full px-3.5 py-2.5 pr-10 text-xs sm:text-sm rounded-xl border {{ $errors->has('email') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-teal-500' }} focus:outline-none focus:ring-2 focus:ring-teal-500/20 text-slate-800 transition"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            value="password123"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••••••"
                            class="w-full px-3.5 py-2.5 pr-10 text-xs sm:text-sm rounded-xl border {{ $errors->has('password') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-teal-500' }} focus:outline-none focus:ring-2 focus:ring-teal-500/20 text-slate-800 transition"
                        >
                        <button
                            type="button"
                            id="togglePasswordBtn"
                            onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition"
                            title="Tampilkan / Sembunyikan Kata Sandi"
                        >
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Input Pilihan Peran Pengguna (Sesuai Mockup 4.5 & Kebutuhan F1) -->
                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Peran pengguna
                    </label>
                    <div class="relative">
                        <select
                            id="role"
                            name="role"
                            required
                            class="w-full px-3.5 py-2.5 pr-10 text-xs sm:text-sm rounded-xl border {{ $errors->has('role') ? 'border-red-400 bg-red-50/30' : 'border-slate-200 focus:border-teal-500' }} focus:outline-none focus:ring-2 focus:ring-teal-500/20 text-slate-800 bg-white transition appearance-none"
                        >
                            <option value="faskes" {{ old('role', 'faskes') == 'faskes' ? 'selected' : '' }}>Faskes (Puskesmas / Rumah Sakit / Klinik)</option>
                            <option value="dinkes" {{ old('role') == 'dinkes' ? 'selected' : '' }}>Dinas Kesehatan (Dinkes)</option>
                            <option value="penyedia" {{ old('role') == 'penyedia' ? 'selected' : '' }}>Penyedia (Gudang Farmasi / Apotek)</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Administrator Sistem)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <span class="block text-[11px] text-slate-400 mt-1">
                        Pilihan peran: Dinas Kesehatan, Faskes, Penyedia, Admin
                    </span>
                    @error('role')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Opsi Ingat Saya & Lupa Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                            checked
                        >
                        <span class="text-xs text-slate-600 font-medium">Ingat saya</span>
                    </label>
                    <a
                        href="#forgot"
                        onclick="alert('Untuk mengatur ulang kata sandi, hubungi Helpdesk Administrator Dinkes Kota Bandung pada jam kerja (08.00 - 16.00 WIB).')"
                        class="text-xs text-teal-600 hover:text-teal-700 font-medium hover:underline"
                    >
                        Lupa password?
                    </a>
                </div>

                <!-- Tombol Submit Masuk (Mockup 4.5: -> Masuk) -->
                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-semibold rounded-xl text-sm shadow-md shadow-teal-600/20 hover:shadow-lg hover:shadow-teal-600/30 transition flex items-center justify-center gap-2 group cursor-pointer"
                >
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <span>Masuk</span>
                </button>
            </form>

            <!-- Keamanan Data Kredensial -->
            <div class="mt-4 flex items-center justify-center gap-1.5 text-[11px] text-slate-400">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Data kredensial Anda terlindungi oleh enkripsi bersertifikat</span>
            </div>

            <!-- Kartu Akses Demo Cepat (Mempermudah Dosen / Penguji / Evaluator) -->
            <div class="mt-6 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Pintasan Akun Uji Coba:
                    </span>
                    <span class="text-[10px] text-teal-600 font-medium">Klik untuk isi otomatis</span>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button
                        type="button"
                        onclick="setDemoCredentials('ayu.nuraini@puskesmas.go.id', 'password123', 'faskes')"
                        class="text-left px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 text-slate-700 text-[11px] transition"
                    >
                        <span class="font-bold text-teal-700 block">1. Faskes (Mockup)</span>
                        <span class="text-[10px] text-slate-500">Ayu Nuraini</span>
                    </button>
                    <button
                        type="button"
                        onclick="setDemoCredentials('dinkes@bandung.go.id', 'password123', 'dinkes')"
                        class="text-left px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 text-slate-700 text-[11px] transition"
                    >
                        <span class="font-bold text-emerald-700 block">2. Dinkes</span>
                        <span class="text-[10px] text-slate-500">Dr. Hendra Gunawan</span>
                    </button>
                    <button
                        type="button"
                        onclick="setDemoCredentials('penyedia@farmasikota.id', 'password123', 'penyedia')"
                        class="text-left px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 text-slate-700 text-[11px] transition"
                    >
                        <span class="font-bold text-sky-700 block">3. Penyedia</span>
                        <span class="text-[10px] text-slate-500">Budi Santoso</span>
                    </button>
                    <button
                        type="button"
                        onclick="setDemoCredentials('admin@medstock.id', 'password123', 'admin')"
                        class="text-left px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 text-slate-700 text-[11px] transition"
                    >
                        <span class="font-bold text-indigo-700 block">4. Administrator</span>
                        <span class="text-[10px] text-slate-500">Admin Sistem</span>
                    </button>
                </div>
            </div>

            <!-- Fasilitas Belum Terdaftar Box (Mockup 4.5) -->
            <div class="mt-5 text-center p-3 rounded-xl bg-slate-50/60 border border-slate-100 text-xs text-slate-500">
                <span>Fasilitas Anda belum terdaftar?</span><br>
                <a href="#admin-contact" onclick="alert('Hubungi Admin Dinkes di (022) 4233211 atau email dinkes@bandung.go.id')" class="text-teal-600 font-medium hover:underline">
                    Hubungi Administrator Dinas Kesehatan untuk mendapatkan akses
                </a>
            </div>
        </div>

        <!-- Footer Copyright Mockup 4.5 -->
        <div class="pt-4 text-center border-t border-slate-100 text-[11px] text-slate-400">
            © 2026 MedStock.id | Bersama Menjaga Efisiensi Kesehatan
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
            `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
        }
    }

    function setDemoCredentials(email, password, role) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        document.getElementById('role').value = role;
    }
</script>
@endsection
