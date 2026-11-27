@extends('layouts.dashboard')

@section('title', 'Penawaran Surplus - MedStock.id')

@section('dashboard_content')
<div class="space-y-6">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-72 bg-gradient-to-l from-emerald-50/90 to-transparent pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-teal-600 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>PBI-09</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-500 normal-case font-medium">Pencatatan Dokumen Penawaran Surplus</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Penawaran Surplus Obat
                </h1>
                <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                    Catat dokumen penawaran stok obat berlebih (surplus) dari fasilitas kesehatan Anda untuk didistribusikan ke faskes lain yang membutuhkan selama berstatus <span class="font-semibold text-slate-700">tersedia</span>.
                </p>
            </div>

            @if ($canManage)
                <div class="shrink-0">
                    <button
                        type="button"
                        id="btn-open-modal-create"
                        onclick="openCreateModal()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs hover:shadow transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Form Penawaran</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== DYNAMIC API FEEDBACK ALERT ===== --}}
    <div id="api-alert-container" class="hidden"></div>

    {{-- ===== STATS SUMMARY ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Dokumen</p>
                <p id="stat-total-offers" class="text-2xl font-extrabold text-slate-900">0</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Tersedia</p>
                <p id="stat-tersedia-offers" class="text-2xl font-extrabold text-slate-900">0</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Cakupan Akses</p>
                <p class="text-sm font-bold text-slate-800 truncate">
                    {{ auth()->user()->faskes->nama_faskes ?? 'Lintas Fasilitas Kesehatan' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ===== LIST / TABLE ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-slate-800">Daftar Dokumen Penawaran Surplus</h2>
                <span id="table-count-badge" class="text-xs text-slate-400">(Memuat...)</span>
            </div>

            <div class="flex items-center gap-2.5">
                @if (! $canManage)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-medium">
                        Mode Lihat Saja
                    </span>
                @endif

                <button
                    type="button"
                    onclick="loadPenawaranList()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-semibold transition cursor-pointer"
                >
                    <span>Muat Ulang</span>
                </button>
            </div>
        </div>

        <div id="penawaran-loading-state" class="py-16 px-6 text-center">
            <p class="text-sm font-semibold text-slate-700">Memuat dokumen penawaran surplus...</p>
        </div>

        <div id="penawaran-error-state" class="hidden py-16 px-6 text-center">
            <h3 class="text-sm font-bold text-slate-800">Gagal Memuat Dokumen Penawaran</h3>
            <p id="penawaran-error-message" class="text-xs text-slate-500 mt-1 max-w-md mx-auto"></p>
            <button type="button" onclick="loadPenawaranList()" class="mt-4 px-4 py-2 rounded-xl bg-teal-600 text-white text-xs font-semibold cursor-pointer">
                Coba Lagi
            </button>
        </div>

        <div id="penawaran-empty-state" class="hidden py-20 px-6 text-center">
            <h3 class="text-sm font-bold text-slate-800">Belum ada dokumen penawaran surplus</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                {{ $canManage
                    ? 'Fasilitas kesehatan Anda belum mencatat dokumen penawaran surplus. Klik tombol di bawah untuk membuat dokumen pertama.'
                    : 'Belum ada dokumen penawaran surplus yang tercatat dalam sistem.' }}
            </p>
            @if ($canManage)
                <button
                    type="button"
                    onclick="openCreateModal()"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition cursor-pointer"
                >
                    <span>+ Buat Form Penawaran</span>
                </button>
            @endif
        </div>

        <div id="penawaran-table-wrapper" class="hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 w-16">ID</th>
                        @if (! $canManage)
                            <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Fasilitas Kesehatan</th>
                        @endif
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Judul Dokumen</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Status</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Catatan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Waktu Dibuat</th>
                        @if ($canManage)
                            <th class="text-center px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="penawaran-table-body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

@if ($canManage)
    {{-- MODAL: BUAT FORM PENAWARAN --}}
    <div id="modal-create-penawaran" role="dialog" aria-modal="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeCreateModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200">
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Buat Form Penawaran Surplus</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Dicatat atas nama {{ auth()->user()->faskes->nama_faskes }}.</p>
                </div>
                <button type="button" onclick="closeCreateModal()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 cursor-pointer">&times;</button>
            </div>
            <form id="form-create-penawaran" onsubmit="submitCreatePenawaran(event)" class="px-6 py-5 space-y-4">
                <div>
                    <label for="create-judul" class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Dokumen <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" id="create-judul" maxlength="150" placeholder="Contoh: Surplus Antibiotik & Analgesik Q4" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white">
                    <p id="create-judul-error" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>
                <div>
                    <label for="create-catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Penawaran <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <textarea id="create-catatan" rows="3" maxlength="1000" placeholder="Jelaskan kondisi penyimpanan, batch, atau waktu pengambilan..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white resize-none"></textarea>
                    <p id="create-catatan-error" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>
                <div id="create-form-error" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs"></div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium cursor-pointer">Batal</button>
                    <button type="submit" id="btn-submit-create" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs cursor-pointer">Simpan Form</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDIT CATATAN --}}
    <div id="modal-edit-penawaran" role="dialog" aria-modal="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeEditModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-200">
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Edit Catatan Penawaran</h2>
                    <p id="edit-modal-subtitle" class="text-xs text-slate-500 mt-0.5">Perbarui catatan dokumen penawaran surplus.</p>
                </div>
                <button type="button" onclick="closeEditModal()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 cursor-pointer">&times;</button>
            </div>
            <form id="form-edit-penawaran" onsubmit="submitEditPenawaran(event)" class="px-6 py-5 space-y-4">
                <input type="hidden" id="edit-penawaran-id" value="">
                <div>
                    <label for="edit-catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Penawaran</label>
                    <textarea id="edit-catatan" rows="4" maxlength="1000" placeholder="Masukkan catatan terbaru untuk penawaran surplus ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white resize-none"></textarea>
                    <p id="edit-catatan-error" class="text-xs text-red-600 mt-1 hidden"></p>
                </div>
                <div id="edit-form-error" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs"></div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium cursor-pointer">Batal</button>
                    <button type="submit" id="btn-submit-edit" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-xs cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: HAPUS FORM --}}
    <div id="modal-delete-penawaran" role="dialog" aria-modal="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" onclick="closeDeleteModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm border border-slate-200">
            <div class="p-6 text-center">
                <h2 class="text-base font-bold text-slate-900">Hapus Dokumen Penawaran?</h2>
                <p class="text-sm text-slate-500 mt-2">
                    Dokumen penawaran <strong id="delete-penawaran-label" class="text-slate-800"></strong> akan dihapus secara permanen.
                </p>
                <div id="delete-form-error" class="hidden mt-3 p-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs text-left"></div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <input type="hidden" id="delete-penawaran-id" value="">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold cursor-pointer">Batal</button>
                <button type="button" id="btn-submit-delete" onclick="submitDeletePenawaran()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold cursor-pointer">Ya, Hapus</button>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    const PENAWARAN_API_URL = @json(url('/api/penawaran'));
    const LOGIN_URL = @json(route('login'));
    const CAN_MANAGE = @json($canManage);

    let penawaranCache = [];

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(isoString) {
        if (!isoString) return '-';
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) return escapeHtml(isoString);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function renderStatusBadge(status) {
        const normalized = String(status || '').toLowerCase();
        const badgeClass = normalized === 'tersedia'
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-slate-100 text-slate-700 border-slate-200';
        const label = normalized ? normalized.charAt(0).toUpperCase() + normalized.slice(1) : '-';
        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-xs font-semibold ${badgeClass}">${escapeHtml(label)}</span>`;
    }

    function showBannerAlert(type, message) {
        const container = document.getElementById('api-alert-container');
        const cls = type === 'success'
            ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
            : 'bg-red-50 border-red-200 text-red-800';
        container.className = `p-4 rounded-xl border text-sm flex items-center justify-between ${cls}`;
        container.innerHTML = `
            <span>${escapeHtml(message)}</span>
            <button type="button" onclick="this.parentElement.classList.add('hidden')" class="ml-4 font-bold text-base leading-none cursor-pointer">&times;</button>
        `;
        container.classList.remove('hidden');
    }

    async function apiFetch(url, options = {}) {
        const method = (options.method || 'GET').toUpperCase();
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        };
        if (method !== 'GET' && method !== 'HEAD') {
            headers['Content-Type'] = 'application/json';
            headers['X-CSRF-TOKEN'] = getCsrfToken();
        }
        const response = await fetch(url, { credentials: 'same-origin', ...options, method, headers });
        if (response.status === 401) {
            window.location.href = LOGIN_URL;
            throw new Error('Sesi berakhir.');
        }
        let payload = null;
        try { payload = await response.json(); } catch (e) { payload = null; }
        if (!response.ok) {
            const err = new Error((payload && payload.message) ? payload.message : `HTTP ${response.status}`);
            err.status = response.status;
            err.payload = payload;
            throw err;
        }
        return payload;
    }

    function renderPenawaranTable(items) {
        document.getElementById('penawaran-loading-state').classList.add('hidden');
        document.getElementById('penawaran-error-state').classList.add('hidden');

        const total = items.length;
        const tersedia = items.filter(i => String(i.status || '').toLowerCase() === 'tersedia').length;
        document.getElementById('stat-total-offers').textContent = total;
        document.getElementById('stat-tersedia-offers').textContent = tersedia;
        document.getElementById('table-count-badge').textContent = `(${total} dokumen)`;

        const emptyEl = document.getElementById('penawaran-empty-state');
        const tableWrapperEl = document.getElementById('penawaran-table-wrapper');
        const tbody = document.getElementById('penawaran-table-body');

        if (!items.length) {
            tableWrapperEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            return;
        }

        emptyEl.classList.add('hidden');
        tableWrapperEl.classList.remove('hidden');

        tbody.innerHTML = items.map(item => {
            const isEditable = String(item.status || '').toLowerCase() === 'tersedia';
            const faskesCol = !CAN_MANAGE
                ? `<td class="px-6 py-4">
                        <span class="font-semibold text-slate-800 block">${escapeHtml(item.faskes ? item.faskes.nama_faskes : '-')}</span>
                        <span class="text-[11px] text-slate-400">${escapeHtml(item.faskes ? `${item.faskes.jenis} • ${item.faskes.kota}` : '')}</span>
                   </td>`
                : '';

            let actionCol = '';
            if (CAN_MANAGE) {
                actionCol = isEditable
                    ? `<td class="px-6 py-4">
                           <div class="flex items-center justify-center gap-2">
                               <button type="button" onclick="openEditModal(${Number(item.id)})" class="px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 border border-teal-200 text-teal-700 text-xs font-semibold cursor-pointer">Edit Catatan</button>
                               <button type="button" onclick="openDeleteModal(${Number(item.id)})" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-semibold cursor-pointer">Hapus</button>
                           </div>
                       </td>`
                    : `<td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 text-[11px] font-medium">Terkunci</span></td>`;
            }

            return `
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 text-xs font-mono font-semibold text-slate-600">#${escapeHtml(item.id)}</td>
                    ${faskesCol}
                    <td class="px-6 py-4 font-semibold text-slate-800">${escapeHtml(item.judul || 'Penawaran Surplus Obat')}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${renderStatusBadge(item.status)}</td>
                    <td class="px-6 py-4 text-xs sm:text-sm text-slate-600 max-w-xs">
                        <span class="line-clamp-2">${item.catatan ? escapeHtml(item.catatan) : '<span class="text-slate-400">&mdash;</span>'}</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-mono text-slate-500 whitespace-nowrap">${formatDateTime(item.created_at)}</td>
                    ${actionCol}
                </tr>
            `;
        }).join('');
    }

    async function loadPenawaranList() {
        document.getElementById('penawaran-loading-state').classList.remove('hidden');
        document.getElementById('penawaran-error-state').classList.add('hidden');
        document.getElementById('penawaran-empty-state').classList.add('hidden');
        document.getElementById('penawaran-table-wrapper').classList.add('hidden');

        try {
            const result = await apiFetch(PENAWARAN_API_URL);
            penawaranCache = Array.isArray(result && result.data) ? result.data : [];
            renderPenawaranTable(penawaranCache);
        } catch (error) {
            document.getElementById('penawaran-loading-state').classList.add('hidden');
            document.getElementById('penawaran-error-message').textContent = error.message || 'Gagal memuat daftar penawaran.';
            document.getElementById('penawaran-error-state').classList.remove('hidden');
        }
    }

    function openCreateModal() {
        if (!CAN_MANAGE) return;
        document.getElementById('form-create-penawaran').reset();
        document.getElementById('create-form-error').classList.add('hidden');
        document.getElementById('modal-create-penawaran').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        const m = document.getElementById('modal-create-penawaran');
        if (m) m.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitCreatePenawaran(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-submit-create');
        btn.disabled = true;
        try {
            const result = await apiFetch(PENAWARAN_API_URL, {
                method: 'POST',
                body: JSON.stringify({
                    judul: document.getElementById('create-judul').value.trim() || null,
                    catatan: document.getElementById('create-catatan').value.trim() || null,
                }),
            });
            closeCreateModal();
            showBannerAlert('success', (result && result.message) || 'Dokumen penawaran surplus berhasil dibuat.');
            await loadPenawaranList();
        } catch (error) {
            const errEl = document.getElementById('create-form-error');
            errEl.textContent = error.message || 'Gagal membuat form penawaran.';
            errEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    }

    function openEditModal(id) {
        if (!CAN_MANAGE) return;
        const found = penawaranCache.find(i => Number(i.id) === Number(id));
        document.getElementById('edit-penawaran-id').value = id;
        document.getElementById('edit-modal-subtitle').textContent = `Perbarui catatan untuk Dokumen Penawaran #${id}.`;
        document.getElementById('edit-catatan').value = found && found.catatan ? found.catatan : '';
        document.getElementById('edit-form-error').classList.add('hidden');
        document.getElementById('modal-edit-penawaran').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const m = document.getElementById('modal-edit-penawaran');
        if (m) m.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitEditPenawaran(event) {
        event.preventDefault();
        const id = document.getElementById('edit-penawaran-id').value;
        const btn = document.getElementById('btn-submit-edit');
        btn.disabled = true;
        try {
            const result = await apiFetch(`${PENAWARAN_API_URL}/${encodeURIComponent(id)}`, {
                method: 'PUT',
                body: JSON.stringify({
                    catatan: document.getElementById('edit-catatan').value.trim() || null,
                }),
            });
            closeEditModal();
            showBannerAlert('success', (result && result.message) || 'Catatan penawaran berhasil diperbarui.');
            await loadPenawaranList();
        } catch (error) {
            const errEl = document.getElementById('edit-form-error');
            errEl.textContent = error.message || 'Gagal memperbarui catatan.';
            errEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    }

    function openDeleteModal(id) {
        if (!CAN_MANAGE) return;
        document.getElementById('delete-penawaran-id').value = id;
        document.getElementById('delete-penawaran-label').textContent = `#${id}`;
        document.getElementById('delete-form-error').classList.add('hidden');
        document.getElementById('modal-delete-penawaran').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const m = document.getElementById('modal-delete-penawaran');
        if (m) m.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitDeletePenawaran() {
        const id = document.getElementById('delete-penawaran-id').value;
        const btn = document.getElementById('btn-submit-delete');
        btn.disabled = true;
        try {
            const result = await apiFetch(`${PENAWARAN_API_URL}/${encodeURIComponent(id)}`, {
                method: 'DELETE',
            });
            closeDeleteModal();
            showBannerAlert('success', (result && result.message) || 'Dokumen penawaran berhasil dihapus.');
            await loadPenawaranList();
        } catch (error) {
            const errEl = document.getElementById('delete-form-error');
            errEl.textContent = error.message || 'Gagal menghapus dokumen penawaran.';
            errEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', loadPenawaranList);
</script>
@endpush
@endsection
