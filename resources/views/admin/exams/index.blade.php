@extends('layouts.app')
@section('title', 'Kelola Paket Ujian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-line">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Paket Ujian</h1>
            <p class="text-sm text-ink/50">Tambah, edit paket ujian dan atur butir-butir soalnya.</p>
        </div>
        <button onclick="openExamModal()" class="btn-primary text-sm px-4 py-2">
            + Tambah Paket Ujian
        </button>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 border-b border-line text-[11px] font-semibold uppercase tracking-wide text-ink/40">
                <tr>
                    <th class="p-4">Kategori</th>
                    <th class="p-4">Subkategori</th>
                    <th class="p-4">Judul Ujian</th>
                    <th class="p-4 text-center">Jumlah Soal</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="examTableBody" class="divide-y divide-line">
                <tr><td colspan="5" class="p-4 text-center text-ink/40">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Tambah / Edit Exam -->
<div id="examModal" class="fixed inset-0 bg-slate-900/50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl">
        <h3 id="examModalTitle" class="text-lg font-bold text-slate-900">Tambah Paket Ujian</h3>
        <form id="examForm" class="space-y-3">
            <input type="hidden" id="examId" value="">
            <div>
                <label class="block text-xs font-semibold uppercase text-ink/50">Kategori</label>
                <select id="category" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
                    <option value="Bahasa">Bahasa</option>
                    <option value="IT">IT</option>
                    <option value="Karakter">Karakter</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-ink/50">Subkategori</label>
                <input type="text" id="subcategory" placeholder="Contoh: Programming / Inggris" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-ink/50">Judul Ujian</label>
                <input type="text" id="title" placeholder="Contoh: Ujian Pemrograman Dasar" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-ink/50">Deskripsi (Opsional)</label>
                <textarea id="description" class="w-full rounded-lg border border-line p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" rows="2"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeExamModal()" class="px-4 py-2 border border-line rounded-lg text-sm">Batal</button>
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
                tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-ink/40">Belum ada paket ujian.</td></tr>';
                return;
            }

            examsCache = result.data;
            tbody.innerHTML = result.data.map(exam => `
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4 font-semibold text-brand-blue">${escapeHtml(exam.category)}</td>
                    <td class="p-4">${escapeHtml(exam.subcategory)}</td>
                    <td class="p-4 font-medium text-slate-900">${escapeHtml(exam.title)}</td>
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

    function openExamModal(id = null) {
        const form = document.getElementById('examForm');
        form.reset();
        document.getElementById('examId').value = id || '';

        if (id) {
            const exam = examsCache.find(e => e.id === id);
            if (!exam) return;
            document.getElementById('examModalTitle').innerText = 'Edit Paket Ujian';
            document.getElementById('examSubmitBtn').innerText = 'Simpan Perubahan';
            document.getElementById('category').value = exam.category;
            document.getElementById('subcategory').value = exam.subcategory;
            document.getElementById('title').value = exam.title;
            document.getElementById('description').value = exam.description || '';
        } else {
            document.getElementById('examModalTitle').innerText = 'Tambah Paket Ujian';
            document.getElementById('examSubmitBtn').innerText = 'Simpan';
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
        if (res.ok) fetchExams();
    }

    fetchExams();
</script>
@endsection
