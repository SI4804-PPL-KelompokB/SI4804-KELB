<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - MedStock.id</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            teal: '#0d9488',
                            tealDark: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Desktop sidebar collapsed state */
        @media (min-width: 1024px) {
            body.sidebar-collapsed #app-sidebar {
                width: 5rem;
            }
            body.sidebar-collapsed #app-sidebar .sidebar-label,
            body.sidebar-collapsed #app-sidebar .sidebar-section-title,
            body.sidebar-collapsed #app-sidebar .sidebar-user-meta {
                display: none;
            }
            body.sidebar-collapsed #app-sidebar .sidebar-nav-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            body.sidebar-collapsed #app-sidebar .sidebar-brand-wrap {
                justify-content: center;
            }
            body.sidebar-collapsed #app-sidebar .sidebar-user-card {
                justify-content: center;
                padding: 0.5rem;
            }
            body.sidebar-collapsed #sidebar-collapse-icon {
                transform: rotate(180deg);
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex">
@php
    $authUser = auth()->user();
    $userRole = strtolower($authUser->role ?? '');

    // Konfigurasi navigasi berbasis peran (hanya modul yang telah diimplementasikan)
    $navItems = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard', '*.dashboard'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        ],
        [
            'label' => 'Manajemen Wilayah',
            'route' => 'wilayah.index',
            'active' => request()->routeIs('wilayah.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />',
        ],
        [
            'label' => 'Inbox Pesan',
            'route' => 'inbox.index',
            'active' => request()->routeIs('inbox.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
        ],
    ];

    if ($userRole === 'admin') {
        $navItems[] = [
            'label' => 'Kategori Obat',
            'route' => 'categories.index',
            'active' => request()->routeIs('categories.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
        ];
    }

    if ($userRole === 'faskes') {
        $navItems[] = [
            'label' => 'Permintaan Darurat',
            'route' => 'permintaan.index',
            'active' => request()->routeIs('permintaan.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />',
        ];
    }

    if ($userRole === 'penyedia') {
        $navItems[] = [
            'label' => 'Data Kendaraan',
            'route' => 'penyedia.kendaraan.index',
            'active' => request()->routeIs('penyedia.kendaraan.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />',
        ];
    }
@endphp

    <!-- Mobile Sidebar Backdrop -->
    <div
        id="sidebar-backdrop"
        onclick="closeMobileSidebar()"
        class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden transition-opacity"
        aria-hidden="true"
    ></div>

    <!-- Left Navigation Sidebar -->
    <aside
        id="app-sidebar"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col transition-all duration-200 -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen shrink-0"
        aria-label="Navigasi Utama"
    >
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-4 border-b border-slate-100 flex items-center justify-between sidebar-brand-wrap shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div class="sidebar-label truncate">
                    <span class="text-lg font-extrabold tracking-tight text-slate-900">
                        MedStock<span class="text-teal-600">.id</span>
                    </span>
                    <span class="block text-[10px] font-medium text-slate-400 -mt-1">Logistik Obat Darurat</span>
                </div>
            </a>

            <!-- Mobile Close Button -->
            <button
                type="button"
                onclick="closeMobileSidebar()"
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                aria-label="Tutup menu navigasi"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Links -->
        <nav class="flex-1 px-3 py-5 space-y-1.5 overflow-y-auto">
            <p class="sidebar-section-title px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Menu Operasional
            </p>

            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    title="{{ $item['label'] }}"
                    class="sidebar-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $item['active']
                        ? 'bg-teal-50 text-teal-700 border border-teal-200/80 shadow-2xs'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent' }}"
                >
                    <svg class="w-5 h-5 shrink-0 {{ $item['active'] ? 'text-teal-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $item['icon'] !!}
                    </svg>
                    <span class="sidebar-label truncate">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <!-- Sidebar Footer: User Profile Summary & Collapse Toggle -->
        <div class="p-3 border-t border-slate-100 space-y-2.5 shrink-0">
            <div class="sidebar-user-card flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-xs border border-teal-200 shrink-0">
                    {{ strtoupper(substr($authUser->name, 0, 2)) }}
                </div>
                <div class="sidebar-user-meta min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ $authUser->name }}</p>
                    <p class="text-[11px] font-medium text-teal-700 truncate">{{ $authUser->role_label }}</p>
                </div>
            </div>

            <!-- Desktop Collapse Button -->
            <button
                type="button"
                id="btn-toggle-sidebar-collapse"
                onclick="toggleDesktopSidebar()"
                class="hidden lg:flex w-full items-center justify-center gap-2 px-3 py-2 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 text-xs font-semibold transition cursor-pointer"
                title="Perkecil atau perluas sidebar"
            >
                <svg id="sidebar-collapse-icon" class="w-4 h-4 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <span class="sidebar-label">Perkecil Menu</span>
            </button>
        </div>
    </aside>

    <!-- Right Shell: Top Header + Main Content + Footer -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                <!-- Left: Mobile Hamburger + Facility / Authority Context -->
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        type="button"
                        id="btn-open-mobile-sidebar"
                        onclick="openMobileSidebar()"
                        class="lg:hidden p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition cursor-pointer"
                        aria-label="Buka menu navigasi"
                        aria-controls="app-sidebar"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @if ($authUser->faskes)
                        <div class="flex items-center gap-2 text-xs min-w-0">
                            <span class="font-bold text-slate-800 truncate">{{ $authUser->faskes->nama_faskes }}</span>
                            <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-semibold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $authUser->faskes->jenis }} Terverifikasi
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-xs text-slate-600 min-w-0">
                            <span class="font-bold text-slate-800 truncate">{{ $authUser->role_label }}</span>
                            <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-medium shrink-0">
                                Otoritas Terpusat
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Right: User Summary & Logout Action -->
                <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                    <div class="hidden md:block text-right">
                        <p class="text-xs font-bold text-slate-800 leading-tight">{{ $authUser->name }}</p>
                        <p class="text-[11px] text-slate-500 leading-tight">{{ $authUser->jabatan ?? $authUser->role_label }}</p>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button
                            type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem MedStock.id?')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-semibold transition cursor-pointer"
                            title="Keluar dari akun Anda"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Flash Alert Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-sm font-bold cursor-pointer">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 text-sm font-bold cursor-pointer">&times;</button>
                </div>
            @endif

            @yield('dashboard_content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 mt-auto py-4 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>&copy; 2026 MedStock.id &bull; Pelaporan Cepat &amp; Distribusi Obat Darurat</span>
                <span class="text-slate-400">Dinas Kesehatan Kota Bandung</span>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            const STORAGE_KEY = 'medstock_sidebar_collapsed';
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            if (localStorage.getItem(STORAGE_KEY) === '1') {
                document.body.classList.add('sidebar-collapsed');
            }

            window.toggleDesktopSidebar = function () {
                document.body.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem(STORAGE_KEY, isCollapsed ? '1' : '0');
            };

            window.openMobileSidebar = function () {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            window.closeMobileSidebar = function () {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            };

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) {
                    backdrop.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>

