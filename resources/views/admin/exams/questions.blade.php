@extends('layouts.app')
@section('title', 'Kelola Butir Soal')

@section('content')
<div class="max-w-5xl mx-auto mt-6 sm:mt-8 pb-12 px-4 sm:px-6 space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div>
            <a href="{{ route('admin.exams.index') }}" class="text-xs text-indigo-600 hover:underline font-semibold">← Kembali ke List Ujian</a>
            <h1 id="examTitle" class="text-2xl font-bold text-slate-900 mt-1">Kelola Soal Ujian</h1>
        </div>
        <button onclick="openQuestionModal()" class="btn-primary text-sm px-4 py-2">
            + Tambah Soal
        </button>
    </div>

    <div id="questionsContainer" class="space-y-4">
        <div class="bg-white p-6 rounded-xl text-center text-slate-400 border border-slate-200">Memuat soal...</div>
    </div>
</div>

<!-- Modal Tambah / Edit Soal -->
<div id="questionModal" class="fixed inset-0 bg-slate-900/50 hidden flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 space-y-4 my-8 shadow-xl">
        <h3 id="questionModalTitle" class="text-lg font-bold text-slate-900">Tambah Soal Baru</h3>
        <form id="questionForm" class="space-y-3">
            <input type="hidden" id="questionId" value="">
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Tipe Soal</label>
                    <select id="type" onchange="toggleQuestionType(this.value)" class="w-full border rounded-lg p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
                        <option value="multiple_choice">Pilihan Ganda (PG)</option>
                        <option value="essay">Esai (Teks / Cerita)</option>
                        <option value="image_upload">Upload Gambar (G)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Waktu (Detik)</label>
                    <input type="number" id="time_limit_seconds" min="5" max="1800" value="60" placeholder="60" class="w-full border rounded-lg p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Bagian GCLWAMA</label>
                    <select id="gclwama_tag" class="w-full border rounded-lg p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40">
                        <option value="">Non-GCLWAMA</option>
                        <option value="G">G - Gambar</option>
                        <option value="C">C - Cerita</option>
                        <option value="L">L - Layout</option>
                        <option value="W">W - Warna</option>
                        <option value="A_animasi">A - Animasi</option>
                        <option value="M">M - Matematika</option>
                        <option value="A_algoritma">A - Algoritma</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500">Teks Pertanyaan / Instruksi Tugas</label>
                <textarea id="question_text" class="w-full border rounded-lg p-2 text-sm mt-1 focus:ring-2 focus:ring-brand-blue/40" rows="3" required></textarea>
            </div>

            <!-- Bagian Opsi PG -->
            <div id="mcSection" class="space-y-2">
                <label class="block text-xs font-semibold uppercase text-slate-500">Pilihan Jawaban (A, B, C, D)</label>
                <input type="text" id="opt_0" placeholder="Pilihan A" class="w-full border rounded-lg p-2 text-sm">
                <input type="text" id="opt_1" placeholder="Pilihan B" class="w-full border rounded-lg p-2 text-sm">
                <input type="text" id="opt_2" placeholder="Pilihan C" class="w-full border rounded-lg p-2 text-sm">
                <input type="text" id="opt_3" placeholder="Pilihan D" class="w-full border rounded-lg p-2 text-sm">

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mt-2">Kunci Jawaban Benar</label>
                    <input type="text" id="correct_answer" placeholder="Harus sama persis dengan salah satu opsi di atas" class="w-full border rounded-lg p-2 text-sm mt-1">
                </div>
            </div>

            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="closeQuestionModal()" class="px-4 py-2 border rounded-lg text-sm font-semibold">Batal</button>
                <button type="submit" id="questionSubmitBtn" class="btn-primary text-sm px-4 py-2">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>

<script>
    const examId = "{{ $examId }}";
    const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
    let questionsCache = [];

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    async function loadQuestions() {
        try {
            const res = await fetch(`/api/admin/exams/${examId}/questions`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (!res.ok) return;

            document.getElementById('examTitle').innerText = `${result.data.exam.title} (${result.data.exam.subcategory})`;
            const container = document.getElementById('questionsContainer');
            questionsCache = result.data.questions || [];

            if (!questionsCache.length) {
                container.innerHTML = '<div class="bg-white p-6 rounded-xl text-center text-slate-400 border border-slate-200">Belum ada soal pada ujian ini.</div>';
                return;
            }

            container.innerHTML = questionsCache.map((q, idx) => `
                <div class="bg-white p-5 rounded-xl border border-slate-200 space-y-3">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-block text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded ${
                                q.type === 'multiple_choice' ? 'bg-amber-100 text-amber-800' : (q.type === 'image_upload' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800')
                            }">
                                ${q.type === 'multiple_choice' ? 'Pilihan Ganda' : (q.type === 'image_upload' ? 'Upload Gambar' : 'Esai')}
                            </span>
                            <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">⏱ ${q.time_limit_seconds || 60}s</span>
                            ${q.gclwama_tag ? `<span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-indigo-100 text-indigo-800">Tag: ${q.gclwama_tag}</span>` : ''}
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button onclick="openQuestionModal(${q.id})" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-1 rounded-lg">Edit</button>
                            <button onclick="deleteQuestion(${q.id})" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1">Hapus</button>
                        </div>
                    </div>
                    <p class="font-medium text-slate-900">${idx + 1}. ${escapeHtml(q.question_text)}</p>
                    ${q.type === 'multiple_choice' && q.options ? `
                        <div class="grid grid-cols-2 gap-2 text-sm text-slate-600 pt-2">
                            ${q.options.map(opt => `
                                <div class="p-2 rounded border ${opt === q.correct_answer ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-semibold' : 'border-slate-100 bg-slate-50'}">
                                    ${escapeHtml(opt)} ${opt === q.correct_answer ? '✓ (Kunci)' : ''}
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
            `).join('');
        } catch (err) {
            console.error(err);
        }
    }

    function openQuestionModal(id = null) {
        const form = document.getElementById('questionForm');
        form.reset();
        document.getElementById('questionId').value = id || '';

        if (id) {
            const q = questionsCache.find(item => item.id === id);
            if (!q) return;
            document.getElementById('questionModalTitle').innerText = 'Edit Soal';
            document.getElementById('questionSubmitBtn').innerText = 'Simpan Perubahan';
            document.getElementById('type').value = q.type;
            document.getElementById('time_limit_seconds').value = q.time_limit_seconds || 60;
            document.getElementById('gclwama_tag').value = q.gclwama_tag || '';
            document.getElementById('question_text').value = q.question_text;
            toggleQuestionType(q.type);
            if (q.type === 'multiple_choice' && q.options) {
                q.options.forEach((opt, i) => {
                    const el = document.getElementById(`opt_${i}`);
                    if (el) el.value = opt;
                });
                document.getElementById('correct_answer').value = q.correct_answer || '';
            }
        } else {
            document.getElementById('questionModalTitle').innerText = 'Tambah Soal Baru';
            document.getElementById('questionSubmitBtn').innerText = 'Simpan Soal';
            document.getElementById('time_limit_seconds').value = 60;
            toggleQuestionType('multiple_choice');
        }

        document.getElementById('questionModal').classList.remove('hidden');
    }

    function closeQuestionModal() {
        document.getElementById('questionModal').classList.add('hidden');
    }

    document.getElementById('questionForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('questionSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan...';

        const id = document.getElementById('questionId').value;
        const type = document.getElementById('type').value;
        let options = null;
        let correct = null;

        if (type === 'multiple_choice') {
            options = [
                document.getElementById('opt_0').value,
                document.getElementById('opt_1').value,
                document.getElementById('opt_2').value,
                document.getElementById('opt_3').value,
            ].filter(Boolean);
            correct = document.getElementById('correct_answer').value;
        }

        const body = {
            type,
            time_limit_seconds: parseInt(document.getElementById('time_limit_seconds').value) || 60,
            gclwama_tag: document.getElementById('gclwama_tag').value || null,
            question_text: document.getElementById('question_text').value,
            options,
            correct_answer: correct,
        };

        if (!id) body.exam_id = examId;

        const url = id ? `/api/admin/questions/${id}` : '/api/admin/questions';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method,
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });

            if (res.ok) {
                closeQuestionModal();
                loadQuestions();
                if (window.notifySuccess) {
                    window.notifySuccess(id ? 'Butir soal berhasil diperbarui!' : 'Butir soal berhasil ditambahkan!');
                }
            } else {
                const err = await res.json().catch(() => ({}));
                alert(err.message || 'Gagal menyimpan soal');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan saat mengirim data.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = id ? 'Simpan Perubahan' : 'Simpan Soal';
        }
    });

    async function deleteQuestion(id) {
        if (!confirm('Hapus soal ini?')) return;
        const res = await fetch(`/api/admin/questions/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (res.ok) {
            loadQuestions();
            if (window.notifySuccess) {
                window.notifySuccess('Butir soal berhasil dihapus!');
            }
        }
    }

    function toggleQuestionType(val) {
        document.getElementById('mcSection').classList.toggle('hidden', val !== 'multiple_choice');
    }

    loadQuestions();
</script>
@endsection