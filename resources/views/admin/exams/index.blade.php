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

<!-- Modal 1: Mulai Gelombang Serentak (Support Ketik Manual & Datalist Rekomendasi) -->
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
                <!-- Datalist untuk autocomplete/rekomendasi -->
                <datalist id="period_suggestions"></datalist>
                <span class="text-[10px] text-slate-400 mt-1 block">Anda bisa mengetik nama periode baru atau memilih rekomendasi di atas.</span>
            </div>

            <div class="space-y-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                <p class="text-[11px] font-bold text-slate-700">Waktu Pelaksanaan Bersama</p>
                <div>
                    <label class="block text-[10px] font-semibold uppercase text-slate-400">Waktu Buka (Mulai)</label>
                    <input type="datetime-local" id="bulk_start_time" class="w-full rounded-lg border border-line p-2 text-xs bg-white mt-0.5">
                    <span class="text-[10px] text-slate-400">Kosongkan jika ingin langsung dibuka sekarang.</span>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase text-slate-400">Waktu Tutup (Selesai)</label>
                    <input type="datetime-local" id="bulk_end_time" class="w-full rounded-lg border border-line p-2 text-xs bg-white mt-0.5">
                    <span class="text-[10px] text-slate-400">Kosongkan jika ujian dibuka tanpa batas penutupan otomatis.</span>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeBulkStartModal()" class="px-4 py-2 border border-line rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" id="bulkSubmitBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2 rounded-xl transition">
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
                    <select id="category" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
                        <option value="Bahasa">Bahasa</option>
                        <option value="IT">IT</option>
                        <option value="Karakter">Karakter</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Subkategori</label>
                    <input type="text" id="subcategory" placeholder="Contoh: GCLWAMA / Inggris" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">Judul Ujian</label>
                <input type="text" id="title" placeholder="Contoh: Ujian Pemetaan Bakat IT" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
            </div>

            {{-- Pengaturan Periode PSB & Waktu Buka Tutup --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-indigo-900">Judul Periode / Gelombang</label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                        <input type="checkbox" id="is_active" class="rounded text-brand-blue" checked>
                        <span>Aktifkan Ujian</span>
                    </label>
                </div>
                <input type="text" id="period_title" placeholder="Contoh: PSB Gelombang 1 - 2026/2027" class="w-full rounded-lg border border-line p-2 text-sm bg-white focus:ring-2 focus:ring-brand-blue/40">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500">Waktu Mulai Buka</label>
                        <input type="datetime-local" id="start_time" class="w-full rounded-lg border border-line p-1.5 text-xs bg-white mt-1">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500">Waktu Ditutup</label>
                        <input type="datetime-local" id="end_time" class="w-full rounded-lg border border-line p-1.5 text-xs bg-white mt-1">
                    </div>
                </div>
                <p class="text-[10px] text-slate-400">Kosongkan rentang waktu jika ingin membuka ujian secara permanen.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">Deskripsi (Opsional)</label>
                <textarea id="description" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" rows="2"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeExamModal()" class="px-4 py-2 border border-line rounded-lg text-sm font-semibold">Batal</button>
                <button type="submit" id="examSubmitBtn" class="btn-primary text-sm px-4 py-2">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
    let examsCache = [];

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
            tbody.innerHTML = result.data.map(exam => `
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4">
                        <span class="font-bold text-brand-blue">${escapeHtml(exam.category)}</span>
                        <p class="text-[11px] font-semibold text-slate-500">${escapeHtml(exam.period_title || 'PSB')}</p>
                    </td>
                    <td class="p-4">${escapeHtml(exam.subcategory)}</td>
                    <td class="p-4 font-medium text-slate-900">${escapeHtml(exam.title)}</td>
                    <td class="p-4 text-center">
                        <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full ${exam.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}">
                            ${exam.is_active ? 'Aktif' : 'Non-aktif'}
                        </span>
                        <p class="text-[10px] text-slate-400 mt-1">
                            ${exam.start_time ? exam.start_time.substring(0,10) : 'Kapanpun'} s/d ${exam.end_time ? exam.end_time.substring(0,10) : 'Kapanpun'}
                        </p>
                    </td>
                    <td class="p-4 text-center">
                        <span class="bg-slate-100 text-slate-700 font-bold px-2.5 py-0.5 rounded-full text-xs">${exam.questions_count || 0}</span>
                    </td>
                    <td class="p-4 text-right space-x-1">
                        <a href="/admin/exams/${exam.id}/questions" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-3 py-1.5 rounded-lg inline-block">Kelola Soal</a>
                        <button onclick="openExamModal(${exam.id})" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-1.5 rounded-lg">Edit</button>
                        <button onclick="deleteExam(${exam.id})" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1.5">Hapus</button>
                    </td>
                </tr>
            `).join('');
        } catch (err) {
            console.error(err);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Modal Mulai Gelombang Serentak
    function openBulkStartModal() {
        const datalist = document.getElementById('period_suggestions');
        datalist.innerHTML = '';

        // Kumpulkan saran nama gelombang yang sudah pernah dibuat
        const distinctPeriods = Array.from(new Set(examsCache.map(e => e.period_title).filter(Boolean)));
        distinctPeriods.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p;
            datalist.appendChild(opt);
        });

        // Set input teks default
        document.getElementById('bulk_period_title').value = distinctPeriods[0] || 'PSB 2026/2027';

        // Default start time = sekarang
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('bulk_start_time').value = now.toISOString().slice(0, 16);
        document.getElementById('bulk_end_time').value = '';

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
                window.notifySuccess(`Seluruh ujian pada gelombang '${periodTitle}' berhasil dimulai serentak!`);
            }
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.message || 'Gagal memulai gelombang secara massal');
        }
    });

    // Modal Edit / Tambah Paket Ujian
    function openExamModal(id = null) {
        const form = document.getElementById('examForm');
        form.reset();
        document.getElementById('examId').value = id || '';

        if (id) {
            const exam = examsCache.find(e => e.id === id);
            if (!exam) return;
            document.getElementById('examModalTitle').innerText = 'Edit Paket & Jadwal Ujian';
            document.getElementById('examSubmitBtn').innerText = 'Simpan Perubahan';
            document.getElementById('category').value = exam.category;
            document.getElementById('subcategory').value = exam.subcategory;
            document.getElementById('title').value = exam.title;
            document.getElementById('period_title').value = exam.period_title || 'PSB 2026/2027';
            document.getElementById('is_active').checked = exam.is_active;
            document.getElementById('start_time').value = exam.start_time ? exam.start_time.substring(0, 16) : '';
            document.getElementById('end_time').value = exam.end_time ? exam.end_time.substring(0, 16) : '';
            document.getElementById('description').value = exam.description || '';
        } else {
            document.getElementById('examModalTitle').innerText = 'Tambah Paket Ujian';
            document.getElementById('examSubmitBtn').innerText = 'Simpan';
            document.getElementById('period_title').value = 'PSB 2026/2027';
            document.getElementById('is_active').checked = true;
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