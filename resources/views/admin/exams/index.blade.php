@extends('layouts.app')
@section('title', 'Kelola Paket Ujian')

@section('content')
<div class="max-w-6xl mx-auto mt-6 sm:mt-8 pb-12 px-4 sm:px-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-line">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Paket &amp; Jadwal Ujian</h1>
            <p class="text-sm text-ink/50">Atur paket soal yang terhubung ke 3 kartu beranda santri dan jadwal pelaksanaan ujian.</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button onclick="openBulkStartModal()" class="flex-1 sm:flex-initial bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl shadow-xs transition flex items-center justify-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Mulai Gelombang Serentak</span>
            </button>
            <button onclick="openExamModal()" class="flex-1 sm:flex-initial btn-primary text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-xs transition active:scale-95">
                + Tambah Paket
            </button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 border-b border-line text-[11px] font-semibold uppercase tracking-wide text-ink/40">
                <tr>
                    <th class="p-4">Kategori &amp; Subkategori</th>
                    <th class="p-4">Tautan Beranda Santri</th>
                    <th class="p-4">Judul Paket Ujian</th>
                    <th class="p-4 text-center">Status &amp; Jadwal</th>
                    <th class="p-4 text-center">Soal</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="examTableBody" class="divide-y divide-line">
                <tr><td colspan="6" class="p-4 text-center text-ink/40">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Mulai Gelombang Serentak -->
<div id="bulkStartModal" class="fixed inset-0 bg-slate-900/50 hidden flex items-center justify-center p-4 z-50 overflow-y-auto backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Mulai Gelombang Serentak</h3>
                <p class="text-xs text-slate-500">Pilih paket ujian spesifik yang akan dijadwalkan bersama santri.</p>
            </div>
        </div>

        <form id="bulkStartForm" class="space-y-4 pt-1">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Nama Periode / Gelombang Ujian</label>
                <input type="text" 
                       id="bulk_period_title" 
                       list="period_suggestions" 
                       placeholder="Contoh: PSB Gelombang 2 - 2026/2027" 
                       class="w-full rounded-xl border border-line p-2.5 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500 outline-none" 
                       required>
                <datalist id="period_suggestions"></datalist>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold uppercase text-slate-500">Pilih Paket Ujian yang Akan Dijalankan</label>
                    <button type="button" onclick="toggleSelectAllExams()" id="btnSelectAll" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800">Pilih Semua</button>
                </div>
                <div id="bulkExamListContainer" class="max-h-48 overflow-y-auto space-y-2 p-2 bg-slate-50 rounded-xl border border-slate-200">
                    <!-- Terisi otomatis via JS -->
                </div>
            </div>

            <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <p class="text-[11px] font-bold text-slate-700">Waktu Pelaksanaan Bersama</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Mulai Buka</label>
                        <input type="text" id="bulk_start_time" placeholder="Pilih waktu buka..." class="datepicker-input w-full rounded-xl border border-line p-2 text-xs font-semibold bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Selesai Ditutup</label>
                        <input type="text" id="bulk_end_time" placeholder="Pilih waktu tutup..." class="datepicker-input w-full rounded-xl border border-line p-2 text-xs font-semibold bg-white focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeBulkStartModal()" class="px-4 py-2 border border-line rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" id="bulkSubmitBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition">
                    Aktifkan Bersama
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Form Tambah / Edit Exam -->
<div id="examModal" class="fixed inset-0 bg-slate-900/50 hidden flex items-center justify-center p-4 z-50 overflow-y-auto backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 space-y-4 my-6 shadow-xl border border-slate-100">
        <h3 id="examModalTitle" class="text-xl font-bold text-slate-900">Tambah Paket Ujian</h3>
        <form id="examForm" class="space-y-4">
            <input type="hidden" id="examId" value="">
            <input type="hidden" id="category" value="IT">
            <input type="hidden" id="subcategory" value="GCLWAMA">

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Judul Paket Ujian</label>
                <input type="text" id="title" placeholder="Contoh: Ujian Pemetaan Bakat IT Gelombang 1" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold focus:ring-2 focus:ring-brand-blue outline-none" required>
            </div>

            <!-- 🌟 Expandable Slot Selector untuk 3 Kartu Beranda Santri 🌟 -->
            <div class="space-y-2 pt-1">
                <label class="block text-xs font-bold uppercase text-slate-700">Pengaturan Penautan ke Kartu Beranda Santri</label>
                
                <div class="space-y-2">
                    <!-- Opsi 1: Jadikan Tautan Aktif (Bisa Melebar ke Bawah) -->
                    <div class="border border-slate-200 rounded-xl bg-white overflow-hidden transition-all duration-300" id="card_featured_yes">
                        <label class="flex items-start gap-3 p-3 cursor-pointer hover:bg-slate-50/70 transition">
                            <input type="radio" name="is_featured_radio" id="featured_yes" value="1" onchange="toggleFeaturedAccordion(true)" class="mt-0.5 text-amber-600 focus:ring-amber-500">
                            <div class="flex-1">
                                <span class="block text-xs font-bold text-slate-900">Jadikan Tautan Aktif di Beranda</span>
                                <span class="block text-[11px] text-slate-500 leading-snug">Sambungkan paket ini langsung ke salah satu dari 3 kartu ujian yang ada di beranda santri.</span>
                            </div>
                        </label>

                        <!-- Pilihan 3 Slot Ujian Beranda -->
                        <div id="targetSlotSection" class="hidden px-3.5 pb-3.5 pt-2 border-t border-slate-100 bg-amber-50/40 space-y-2.5">
                            <label class="block text-[11px] font-extrabold text-amber-900 uppercase">Pilih Kartu Ujian Beranda yang Dituju:</label>
                            
                            <div class="space-y-2">
                                <!-- Slot 1: IT - GCLWAMA -->
                                <label class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-amber-200 cursor-pointer hover:border-amber-400 transition">
                                    <input type="radio" name="home_slot_choice" value="it_gclwama" onchange="applyHomeSlot('it_gclwama', 'IT', 'GCLWAMA')" class="text-amber-600 focus:ring-amber-500" checked>
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Kartu IT: GCLWAMA</span>
                                        <span class="block text-[10px] text-slate-500">Kategori IT • Subkategori GCLWAMA</span>
                                    </div>
                                </label>

                                <!-- Slot 2: Bahasa - Inggris -->
                                <label class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-amber-200 cursor-pointer hover:border-amber-400 transition">
                                    <input type="radio" name="home_slot_choice" value="bahasa_inggris" onchange="applyHomeSlot('bahasa_inggris', 'Bahasa', 'Inggris')" class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Kartu Bahasa: Bahasa Inggris</span>
                                        <span class="block text-[10px] text-slate-500">Kategori Bahasa • Subkategori Inggris</span>
                                    </div>
                                </label>

                                <!-- Slot 3: Bahasa - Arab -->
                                <label class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-amber-200 cursor-pointer hover:border-amber-400 transition">
                                    <input type="radio" name="home_slot_choice" value="bahasa_arab" onchange="applyHomeSlot('bahasa_arab', 'Bahasa', 'Arab')" class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Kartu Bahasa: Bahasa Arab</span>
                                        <span class="block text-[10px] text-slate-500">Kategori Bahasa • Subkategori Arab</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi 2: Simpan Sebagai Cadangan / Draf -->
                    <div class="border border-slate-200 rounded-xl bg-white" id="card_featured_no">
                        <label class="flex items-start gap-3 p-3 cursor-pointer hover:bg-slate-50/70 transition">
                            <input type="radio" name="is_featured_radio" id="featured_no" value="0" onchange="toggleFeaturedAccordion(false)" class="mt-0.5 text-slate-600 focus:ring-slate-500" checked>
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Simpan Sebagai Cadangan / Ujian Terpisah</span>
                                <span class="block text-[11px] text-slate-500 leading-snug">Paket ini tidak muncul di kartu utama beranda santri dan hanya bisa dibuka via tautan khusus (Salin Link).</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Periode Gelombang & Waktu -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-slate-700">Periode Gelombang</label>
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                        <input type="checkbox" id="is_active" class="rounded text-brand-blue" checked>
                        <span>Paket Aktif</span>
                    </label>
                </div>
                <input type="text" id="period_title" placeholder="Contoh: PSB Gelombang 1 - 2026/2027" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold bg-white focus:ring-2 focus:ring-brand-blue outline-none">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Mulai Buka</label>
                        <input type="text" id="start_time" placeholder="Pilih waktu buka..." class="datepicker-input w-full rounded-xl border border-line p-2 text-xs font-semibold bg-white focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Selesai Ditutup</label>
                        <input type="text" id="end_time" placeholder="Pilih waktu tutup..." class="datepicker-input w-full rounded-xl border border-line p-2 text-xs font-semibold bg-white focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">Deskripsi (Opsional)</label>
                <textarea id="description" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold mt-1 focus:ring-2 focus:ring-brand-blue outline-none" rows="2"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeExamModal()" class="px-4 py-2 border border-line rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" id="examSubmitBtn" class="btn-primary text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs">Simpan Paket</button>
            </div>
        </form>
    </div>
</div>

<script>
    function getAuthToken() {
        return localStorage.getItem('ts_token') || localStorage.getItem('token') || '';
    }

    let examsCache = [];
    let selectedHomeSlot = 'it_gclwama';

    const fpConfig = {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        altInput: true,
        altFormat: "d M Y - H:i",
        allowInput: false
    };

    let fpBulkStart, fpBulkEnd, fpExamStart, fpExamEnd;

    document.addEventListener('DOMContentLoaded', () => {
        fpBulkStart = flatpickr("#bulk_start_time", fpConfig);
        fpBulkEnd = flatpickr("#bulk_end_time", fpConfig);
        fpExamStart = flatpickr("#start_time", fpConfig);
        fpExamEnd = flatpickr("#end_time", fpConfig);
        fetchExams();
    });

    const slotDisplayBadges = {
        'it_gclwama': '<span class="bg-indigo-100 text-indigo-800 border border-indigo-200 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">★ IT: GCLWAMA</span>',
        'bahasa_inggris': '<span class="bg-sky-100 text-sky-800 border border-sky-200 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">★ Bahasa: Inggris</span>',
        'bahasa_arab': '<span class="bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">★ Bahasa: Arab</span>',
    };

    function toggleFeaturedAccordion(isFeatured) {
        const slotSection = document.getElementById('targetSlotSection');
        const cardYes = document.getElementById('card_featured_yes');

        if (isFeatured) {
            slotSection.classList.remove('hidden');
            cardYes.classList.add('border-amber-400', 'ring-1', 'ring-amber-300');
            const activeChoice = document.querySelector('input[name="home_slot_choice"]:checked');
            if (activeChoice) {
                activeChoice.dispatchEvent(new Event('change'));
            }
        } else {
            slotSection.classList.add('hidden');
            cardYes.classList.remove('border-amber-400', 'ring-1', 'ring-amber-300');
        }
    }

    function applyHomeSlot(slotKey, category, subcategory) {
        selectedHomeSlot = slotKey;
        document.getElementById('category').value = category;
        document.getElementById('subcategory').value = subcategory;
    }

    async function fetchExams() {
        const token = getAuthToken();
        const tbody = document.getElementById('examTableBody');

        try {
            const res = await fetch('/api/admin/exams', {
                headers: { 
                    'Authorization': `Bearer ${token}`, 
                    'Accept': 'application/json' 
                }
            });

            if (res.status === 401) {
                tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-rose-600 font-bold">Sesi Anda telah berakhir. Silakan login kembali.</td></tr>';
                return;
            }

            const result = await res.json();

            if (!result.data || !result.data.length) {
                examsCache = [];
                tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-ink/40">Belum ada paket ujian. Silakan klik tombol "+ Tambah Paket".</td></tr>';
                return;
            }

            examsCache = result.data;
            tbody.innerHTML = result.data.map(exam => {
                const examToken = exam.hash_id || exam.id;
                
                let slotKey = exam.home_slot;
                if (!slotKey && exam.is_featured) {
                    if (exam.category === 'IT') slotKey = 'it_gclwama';
                    else if (exam.subcategory === 'Inggris') slotKey = 'bahasa_inggris';
                    else if (exam.subcategory === 'Arab') slotKey = 'bahasa_arab';
                }

                const slotBadge = slotKey && slotDisplayBadges[slotKey] 
                    ? slotDisplayBadges[slotKey] 
                    : '<span class="text-slate-400 text-xs italic">Cadangan / Draf</span>';

                return `
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4">
                        <span class="font-bold text-brand-blue">${escapeHtml(exam.category)}</span>
                        <p class="text-xs text-slate-600 font-medium">${escapeHtml(exam.subcategory)}</p>
                    </td>
                    <td class="p-4">
                        ${slotBadge}
                    </td>
                    <td class="p-4">
                        <span class="font-bold text-slate-900 block">${escapeHtml(exam.title)}</span>
                        <span class="text-[11px] text-slate-400">${escapeHtml(exam.period_title || 'PSB')}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-block text-[11px] font-bold px-2.5 py-0.5 rounded-full ${exam.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}">
                            ${exam.is_active ? 'Aktif' : 'Non-aktif'}
                        </span>
                        <p class="text-[10px] text-slate-400 mt-1 font-mono">
                            ${exam.start_time ? exam.start_time.substring(0,16) : 'Kapanpun'} s/d ${exam.end_time ? exam.end_time.substring(0,16) : 'Kapanpun'}
                        </p>
                    </td>
                    <td class="p-4 text-center">
                        <span class="bg-slate-100 text-slate-700 font-bold px-2.5 py-0.5 rounded-full text-xs">${exam.questions_count || 0}</span>
                    </td>
                    <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                        <button type="button" onclick="copyExamLink('${examToken}', '${escapeHtml(exam.title)}')" class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-3 py-1.5 rounded-lg border border-emerald-200 transition inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Salin Link</span>
                        </button>
                        <a href="/admin/exams/${examToken}/questions" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-3 py-1.5 rounded-lg inline-block">Kelola Soal</a>
                        <button onclick="openExamModal('${examToken}')" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-1.5 rounded-lg">Edit</button>
                        <button onclick="deleteExam('${examToken}')" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1.5">Hapus</button>
                    </td>
                </tr>
            `}).join('');
        } catch (err) {
            console.error('Fetch exams error:', err);
            tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-rose-600">Gagal mengambil data dari server. Periksa koneksi API Anda.</td></tr>';
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function copyExamLink(token, examTitle) {
        const fullUrl = `${window.location.origin}/ujian/${token}`;
        navigator.clipboard.writeText(fullUrl).then(() => {
            if (window.notifySuccess) {
                window.notifySuccess(`Link ujian "${examTitle}" berhasil disalin ke clipboard!`);
            } else {
                alert(`Link ujian berhasil disalin:\n${fullUrl}`);
            }
        }).catch(() => {
            prompt('Salin link ujian berikut:', fullUrl);
        });
    }

    // ---------- Modal 1: Mulai Gelombang Serentak ----------
    function openBulkStartModal() {
        const datalist = document.getElementById('period_suggestions');
        datalist.innerHTML = '';
        const distinctPeriods = Array.from(new Set(examsCache.map(e => e.period_title).filter(Boolean)));
        distinctPeriods.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p;
            datalist.appendChild(opt);
        });

        document.getElementById('bulk_period_title').value = distinctPeriods[0] || 'PSB 2026/2027';

        const container = document.getElementById('bulkExamListContainer');
        if (!examsCache.length) {
            container.innerHTML = '<p class="text-xs text-slate-400 p-2">Tidak ada paket ujian.</p>';
        } else {
            container.innerHTML = examsCache.map(e => `
                <label class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-slate-200 cursor-pointer hover:border-emerald-400 transition">
                    <input type="checkbox" name="bulk_selected_exams" value="${e.hash_id || e.id}" class="bulk-exam-chk rounded text-emerald-600 focus:ring-emerald-500" checked>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-900 truncate">${escapeHtml(e.title)}</span>
                            ${e.home_slot || e.is_featured ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-black px-1.5 py-0.5 rounded">★ Beranda</span>' : ''}
                        </div>
                        <p class="text-[10px] text-slate-500">${escapeHtml(e.category)} — ${escapeHtml(e.subcategory)} • ${e.questions_count || 0} Soal</p>
                    </div>
                </label>
            `).join('');
        }

        if (fpBulkStart) fpBulkStart.setDate(new Date());
        if (fpBulkEnd) fpBulkEnd.clear();

        document.getElementById('bulkStartModal').classList.remove('hidden');
    }

    function closeBulkStartModal() {
        document.getElementById('bulkStartModal').classList.add('hidden');
    }

    let allSelected = true;
    function toggleSelectAllExams() {
        allSelected = !allSelected;
        document.querySelectorAll('.bulk-exam-chk').forEach(c => c.checked = allSelected);
        document.getElementById('btnSelectAll').innerText = allSelected ? 'Batalkan Semua' : 'Pilih Semua';
    }

    document.getElementById('bulkStartForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = getAuthToken();
        const periodTitle = document.getElementById('bulk_period_title').value.trim();
        
        const selectedIds = Array.from(document.querySelectorAll('.bulk-exam-chk:checked')).map(c => c.value);
        if (!selectedIds.length) {
            alert('Pilih setidaknya satu paket ujian yang akan dijalankan!');
            return;
        }

        const body = {
            period_title: periodTitle,
            exam_ids: selectedIds,
            start_time: document.getElementById('bulk_start_time').value || null,
            end_time: document.getElementById('bulk_end_time').value || null,
        };

        const res = await fetch('/api/admin/exams/bulk-start-period', {
            method: 'POST',
            headers: { 
                'Authorization': `Bearer ${token}`, 
                'Content-Type': 'application/json', 
                'Accept': 'application/json' 
            },
            body: JSON.stringify(body)
        });

        if (res.ok) {
            closeBulkStartModal();
            fetchExams();
            if (window.notifySuccess) {
                window.notifySuccess(`Gelombang '${periodTitle}' berhasil dimulai serentak!`);
            }
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.message || 'Gagal memulai gelombang secara massal');
        }
    });

    // ---------- Modal 2: Tambah / Edit Ujian ----------
    function openExamModal(identifier = null) {
        const form = document.getElementById('examForm');
        form.reset();
        document.getElementById('examId').value = identifier || '';

        if (identifier) {
            const exam = examsCache.find(e => e.hash_id === identifier || String(e.id) === String(identifier));
            if (!exam) return;
            document.getElementById('examModalTitle').innerText = 'Edit Paket Ujian';
            document.getElementById('title').value = exam.title;
            document.getElementById('period_title').value = exam.period_title || 'PSB 2026/2027';
            document.getElementById('is_active').checked = exam.is_active;

            document.getElementById('category').value = exam.category || 'IT';
            document.getElementById('subcategory').value = exam.subcategory || 'GCLWAMA';

            let slotKey = exam.home_slot;
            if (!slotKey && exam.is_featured) {
                if (exam.category === 'IT') slotKey = 'it_gclwama';
                else if (exam.subcategory === 'Inggris') slotKey = 'bahasa_inggris';
                else if (exam.subcategory === 'Arab') slotKey = 'bahasa_arab';
            }

            if (slotKey) {
                document.getElementById('featured_yes').checked = true;
                toggleFeaturedAccordion(true);
                const matchedRadio = document.querySelector(`input[name="home_slot_choice"][value="${slotKey}"]`);
                if (matchedRadio) matchedRadio.checked = true;
                selectedHomeSlot = slotKey;
            } else {
                document.getElementById('featured_no').checked = true;
                toggleFeaturedAccordion(false);
                selectedHomeSlot = null;
            }
            
            if (fpExamStart) fpExamStart.setDate(exam.start_time ? exam.start_time.substring(0, 16) : null);
            if (fpExamEnd) fpExamEnd.setDate(exam.end_time ? exam.end_time.substring(0, 16) : null);

            document.getElementById('description').value = exam.description || '';
        } else {
            document.getElementById('examModalTitle').innerText = 'Tambah Paket Ujian';
            document.getElementById('period_title').value = 'PSB 2026/2027';
            document.getElementById('is_active').checked = true;
            document.getElementById('featured_yes').checked = true;
            toggleFeaturedAccordion(true);
            
            const firstRadio = document.querySelector('input[name="home_slot_choice"][value="it_gclwama"]');
            if (firstRadio) firstRadio.checked = true;
            applyHomeSlot('it_gclwama', 'IT', 'GCLWAMA');

            if (fpExamStart) fpExamStart.clear();
            if (fpExamEnd) fpExamEnd.clear();
        }

        document.getElementById('examModal').classList.remove('hidden');
    }

    function closeExamModal() {
        document.getElementById('examModal').classList.add('hidden');
    }

    document.getElementById('examForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = getAuthToken();
        const id = document.getElementById('examId').value;
        const isFeatured = document.querySelector('input[name="is_featured_radio"]:checked')?.value === '1';

        const body = {
            category: document.getElementById('category').value,
            subcategory: document.getElementById('subcategory').value,
            home_slot: isFeatured ? selectedHomeSlot : null,
            title: document.getElementById('title').value,
            period_title: document.getElementById('period_title').value,
            is_active: document.getElementById('is_active').checked,
            start_time: document.getElementById('start_time').value || null,
            end_time: document.getElementById('end_time').value || null,
            description: document.getElementById('description').value
        };

        const url = id ? `/api/admin/exams/${id}` : '/api/admin/exams';
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: { 
                'Authorization': `Bearer ${token}`, 
                'Content-Type': 'application/json', 
                'Accept': 'application/json' 
            },
            body: JSON.stringify(body)
        });

        if (res.ok) {
            closeExamModal();
            fetchExams();
            if (window.notifySuccess) {
                window.notifySuccess(id ? 'Paket ujian berhasil diperbarui!' : 'Paket ujian baru berhasil disimpan!');
            }
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.message || 'Gagal menyimpan paket ujian');
        }
    });

    async function deleteExam(id) {
        const token = getAuthToken();
        if (!confirm('Hapus paket ujian ini beserta seluruh butir soalnya?')) return;
        const res = await fetch(`/api/admin/exams/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (res.ok) {
            fetchExams();
            if (window.notifySuccess) {
                window.notifySuccess('Paket ujian berhasil dihapus!');
            }
        }
    }
</script>
@endsection