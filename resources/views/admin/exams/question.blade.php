<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Soal - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 p-6">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div>
                <a href="/admin/exams" class="text-xs text-indigo-600 hover:underline font-semibold">← Kembali ke List Ujian</a>
                <h1 id="examTitle" class="text-2xl font-bold text-slate-900 mt-1">Kelola Soal Ujian</h1>
            </div>
            <button onclick="toggleModal(true)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                + Tambah Soal
            </button>
        </div>

        <div id="questionsContainer" class="space-y-4">
            <div class="bg-white p-6 rounded-xl text-center text-slate-400 border border-slate-200">Memuat soal...</div>
        </div>
    </div>

    <!-- Modal Tambah Soal -->
    <div id="questionModal" class="fixed inset-0 bg-slate-900/50 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 my-8">
            <h3 class="text-lg font-bold text-slate-900">Tambah Soal Baru</h3>
            <form id="createQuestionForm" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Tipe Soal</label>
                    <select id="type" onchange="toggleQuestionType(this.value)" class="w-full border rounded-lg p-2 text-sm mt-1" required>
                        <option value="multiple_choice">Pilihan Ganda (PG)</option>
                        <option value="essay">Esai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Teks Soal / Pertanyaan</label>
                    <textarea id="question_text" class="w-full border rounded-lg p-2 text-sm mt-1" rows="3" required></textarea>
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
                        <input type="text" id="correct_answer" placeholder="Harus sama persis dengan salah satu opsi" class="w-full border rounded-lg p-2 text-sm mt-1">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3">
                    <button type="button" onclick="toggleModal(false)" class="px-4 py-2 border rounded-lg text-sm">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const examId = "{{ $examId }}";
        const token = localStorage.getItem('token');

        async function loadQuestions() {
            try {
                const res = await fetch(`/api/admin/exams/${examId}/questions`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const result = await res.json();
                
                if (!res.ok) return;

                document.getElementById('examTitle').innerText = `${result.data.exam.title} (${result.data.exam.subcategory})`;
                const container = document.getElementById('questionsContainer');

                if (result.data.questions.length === 0) {
                    container.innerHTML = '<div class="bg-white p-6 rounded-xl text-center text-slate-400 border border-slate-200">Belum ada soal pada ujian ini.</div>';
                    return;
                }

                container.innerHTML = result.data.questions.map((q, idx) => `
                    <div class="bg-white p-5 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="inline-block text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded ${q.type === 'multiple_choice' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'}">
                                ${q.type === 'multiple_choice' ? 'Pilihan Ganda' : 'Esai'}
                            </span>
                            <button onclick="deleteQuestion(${q.id})" class="text-xs text-rose-600 hover:text-rose-800 font-semibold">Hapus</button>
                        </div>
                        <p class="font-medium text-slate-900">${idx + 1}. ${q.question_text}</p>
                        ${q.type === 'multiple_choice' && q.options ? `
                            <div class="grid grid-cols-2 gap-2 text-sm text-slate-600 pt-2">
                                ${q.options.map(opt => `
                                    <div class="p-2 rounded border ${opt === q.correct_answer ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-semibold' : 'border-slate-100 bg-slate-50'}">
                                        ${opt} ${opt === q.correct_answer ? '✓ (Kunci)' : ''}
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

        document.getElementById('createQuestionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
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
                exam_id: examId,
                type: type,
                question_text: document.getElementById('question_text').value,
                options: options,
                correct_answer: correct
            };

            const res = await fetch('/api/admin/questions', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });

            if (res.ok) {
                toggleModal(false);
                document.getElementById('createQuestionForm').reset();
                toggleQuestionType('multiple_choice');
                loadQuestions();
            } else {
                alert('Gagal menambahkan soal');
            }
        });

        async function deleteQuestion(id) {
            if (!confirm('Hapus soal ini?')) return;
            const res = await fetch(`/api/admin/questions/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (res.ok) loadQuestions();
        }

        function toggleQuestionType(val) {
            document.getElementById('mcSection').classList.toggle('hidden', val === 'essay');
        }

        function toggleModal(show) {
            document.getElementById('questionModal').classList.toggle('hidden', !show);
        }

        loadQuestions();
    </script>
</body>
</html>