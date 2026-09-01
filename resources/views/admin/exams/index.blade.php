@extends('layouts.app')
@section('title', 'Kelola Paket Ujian')

@section('content')
<div class="max-w-6xl mx-auto mt-6 sm:mt-8 pb-12 px-4 sm:px-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-line">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Paket &amp; Jadwal Ujian</h1>
            <p class="text-sm text-ink/50">Atur periode gelombang PSB, jadwal buka-tutup ujian, dan butir soal.</p>
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
                    <th class="p-4">Kategori / Periode</th>
                    <th class="p-4">Subkategori</th>
                    <th class="p-4">Judul Ujian</th>
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
    <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Mulai Gelombang Serentak</h3>
                <p class="text-xs text-slate-500">Ketik nama gelombang baru atau pilih dari yang sudah ada.</p>
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
                <span class="text-[10px] text-slate-400 mt-1 block">Ketik nama periode baru atau pilih rekomendasi.</span>
            </div>

            <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <p class="text-[11px] font-bold text-slate-700">Waktu Pelaksanaan Bersama</p>
                
                <div>
                    <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Buka (Mulai)</label>
                    <div class="relative">
                        <input type="text" id="bulk_start_time" placeholder="Pilih tanggal & waktu..." class="datepicker-input w-full rounded-xl border border-line p-2.5 text-xs font-semibold bg-white pl-9 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Tutup (Selesai)</label>
                    <div class="relative">
                        <input type="text" id="bulk_end_time" placeholder="Pilih tanggal & waktu..." class="datepicker-input w-full rounded-xl border border-line p-2.5 text-xs font-semibold bg-white pl-9 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Kategori</label>
                    <select id="category" class="custom-select w-full rounded-xl border border-line p-2.5 text-xs font-semibold bg-white mt-1 focus:ring-2 focus:ring-brand-blue outline-none" required>
                        <option value="Bahasa">Bahasa</option>
                        <option value="IT">IT</option>
                        <option value="Karakter">Karakter</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Subkategori</label>
                    <input type="text" id="subcategory" placeholder="Contoh: GCLWAMA / Inggris" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold mt-1 focus:ring-2 focus:ring-brand-blue outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">Judul Ujian</label>
                <input type="text" id="title" placeholder="Contoh: Ujian Pemetaan Bakat IT" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold mt-1 focus:ring-2 focus:ring-brand-blue outline-none" required>
            </div>

            <!-- Pengaturan Periode PSB & DatePicker -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-indigo-900">Judul Periode / Gelombang</label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                        <input type="checkbox" id="is_active" class="rounded text-brand-blue" checked>
                        <span>Aktifkan Ujian</span>
                    </label>
                </div>
                <input type="text" id="period_title" placeholder="Contoh: PSB Gelombang 1 - 2026/2027" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold bg-white focus:ring-2 focus:ring-brand-blue outline-none">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Mulai Buka</label>
                        <div class="relative">
                            <input type="text" id="start_time" placeholder="Pilih waktu buka..." class="datepicker-input w-full rounded-xl border border-line p-2 text-xs font-semibold bg-white pl-8 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold uppercase text-slate-400 mb-1">Waktu Ditutup</label>
                        <div class="relative">
                            <input type="text" id="end_time" placeholder="Pilih waktu tutup..." class="datepicker-input w-full rounded-xl border border-line p-2 text-xs font-semibold bg-white pl-8 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">Deskripsi (Opsional)</label>
                <textarea id="description" class="w-full rounded-xl border border-line p-2.5 text-xs font-semibold mt-1 focus:ring-2 focus:ring-brand-blue outline-none" rows="2"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeExamModal()" class="px-4 py-2 border border-line rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" id="examSubmitBtn" class="btn-primary text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
    let examsCache = [];

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
    });

    async function fetchExams() {
        try {
            const res = await fetch('/api/admin/exams', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await res.json();
            const tbody = document.getElementById('examTableBody');

            if (!result.data || !result.data.length) {
                examsCache = [];
                tbody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-ink/40">Belum ada paket ujian.</td></tr>';
                return;
            }

            examsCache = result.data;
            tbody.innerHTML = result.data.map(exam => {
                const examToken = exam.hash_id || exam.id;
                return `
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4">
                        <span class="font-bold text-brand-blue">${escapeHtml(exam.category)}</span>
                        <p class="text-[11px] font-semibold text-slate-500">${escapeHtml(exam.period_title || 'PSB')}</p>
                    </td>
                    <td class="p-4 font-medium text-slate-700">${escapeHtml(exam.subcategory)}</td>
                    <td class="p-4 font-bold text-slate-900">${escapeHtml(exam.title)}</td>
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
                        <!-- 🌟 Tombol Salin Link Ujian Terenkripsi 🌟 -->
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
            console.error(err);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // 🌟 Fungsi Salin Link Ujian Terenkripsi 🌟
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

        if (fpBulkStart) fpBulkStart.setDate(new Date());
        if (fpBulkEnd) fpBulkEnd.clear();

        document.getElementById('bulkStartModal').classList.remove('hidden');
    }

    function closeBulkStartModal() {
        document.getElementById('bulkStartModal').classList.add('hidden');
    }

    document.getElementById('bulkStartForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const periodTitle = document.getElementById('bulk_period_title').value.trim();
        if (!periodTitle) {
            alert('Tuliskan nama periode gelombang terlebih dahulu!');
            return;
        }

        const body = {
            period_title: periodTitle,
            start_time: document.getElementById('bulk_start_time').value || null,
            end_time: document.getElementById('bulk_end_time').value || null,
        };

        const res = await fetch('/api/admin/exams/bulk-start-period', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });

        if (res.ok) {
            closeBulkStartModal();
            fetchExams();
            if (window.notifySuccess) {
                window.notifySuccess(`Seluruh ujian gelombang '${periodTitle}' berhasil dimulai serentak!`);
            }
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.message || 'Gagal memulai gelombang secara massal');
        }
    });

    function openExamModal(identifier = null) {
        const form = document.getElementById('examForm');
        form.reset();
        document.getElementById('examId').value = identifier || '';

        if (identifier) {
            const exam = examsCache.find(e => e.hash_id === identifier || String(e.id) === String(identifier));
            if (!exam) return;
            document.getElementById('examModalTitle').innerText = 'Edit Paket & Jadwal Ujian';
            document.getElementById('category').value = exam.category;
            document.getElementById('subcategory').value = exam.subcategory;
            document.getElementById('title').value = exam.title;
            document.getElementById('period_title').value = exam.period_title || 'PSB 2026/2027';
            document.getElementById('is_active').checked = exam.is_active;
            
            if (fpExamStart) fpExamStart.setDate(exam.start_time ? exam.start_time.substring(0, 16) : null);
            if (fpExamEnd) fpExamEnd.setDate(exam.end_time ? exam.end_time.substring(0, 16) : null);

            document.getElementById('description').value = exam.description || '';
        } else {
            document.getElementById('examModalTitle').innerText = 'Tambah Paket Ujian';
            document.getElementById('period_title').value = 'PSB 2026/2027';
            document.getElementById('is_active').checked = true;
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
        const id = document.getElementById('examId').value;
        const body = {
            category: document.getElementById('category').value,
            subcategory: document.getElementById('subcategory').value,
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
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });

        if (res.ok) {
            closeExamModal();
            fetchExams();
            if (window.notifySuccess) {
                window.notifySuccess(id ? 'Paket ujian berhasil diperbarui!' : 'Paket ujian baru berhasil dibuat!');
            }
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.message || 'Gagal menyimpan paket ujian');
        }
    });

    async function deleteExam(id) {
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

    fetchExams();
</script>
@endsection