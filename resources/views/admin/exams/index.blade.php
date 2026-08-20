<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket Ujian - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 p-6">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Kelola Paket Ujian</h1>
                <p class="text-sm text-slate-500">Tambah dan kelola kategori ujian santri</p>
            </div>
            <button onclick="toggleModal(true)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition">
                + Tambah Paket Ujian
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 font-semibold">
                    <tr>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Subkategori</th>
                        <th class="p-4">Judul Ujian</th>
                        <th class="p-4 text-center">Jumlah Soal</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="examTableBody" class="divide-y divide-slate-100">
                    <tr><td colspan="5" class="p-4 text-center text-slate-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Exam -->
    <div id="examModal" class="fixed inset-0 bg-slate-900/50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Tambah Paket Ujian</h3>
            <form id="createExamForm" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Kategori</label>
                    <select id="category" class="w-full border rounded-lg p-2 text-sm mt-1" required>
                        <option value="Bahasa">Bahasa</option>
                        <option value="IT">IT</option>
                        <option value="Karakter">Karakter</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Subkategori</label>
                    <input type="text" id="subcategory" placeholder="Contoh: Programming / Inggris" class="w-full border rounded-lg p-2 text-sm mt-1" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Judul Ujian</label>
                    <input type="text" id="title" placeholder="Contoh: Ujian Pemrograman Dasar" class="w-full border rounded-lg p-2 text-sm mt-1" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500">Deskripsi (Opsional)</label>
                    <textarea id="description" class="w-full border rounded-lg p-2 text-sm mt-1" rows="2"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="toggleModal(false)" class="px-4 py-2 border rounded-lg text-sm">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');

        async function fetchExams() {
            try {
                const res = await fetch('/api/admin/exams', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const result = await res.json();
                const tbody = document.getElementById('examTableBody');
                
                if (!result.data || result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-400">Belum ada paket ujian.</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(exam => `
                    <tr class="hover:bg-slate-50">
                        <td class="p-4 font-semibold text-indigo-600">${exam.category}</td>
                        <td class="p-4">${exam.subcategory}</td>
                        <td class="p-4 font-medium text-slate-900">${exam.title}</td>
                        <td class="p-4 text-center">
                            <span class="bg-slate-100 text-slate-700 font-bold px-2.5 py-0.5 rounded-full text-xs">${exam.questions_count || 0}</span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <a href="/admin/exams/${exam.id}/questions" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-3 py-1.5 rounded-lg inline-block">Kelola Soal</a>
                            <button onclick="deleteExam(${exam.id})" class="text-xs text-rose-600 hover:text-rose-800 font-semibold">Hapus</button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {
                console.error(err);
            }
        }

        document.getElementById('createExamForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = {
                category: document.getElementById('category').value,
                subcategory: document.getElementById('subcategory').value,
                title: document.getElementById('title').value,
                description: document.getElementById('description').value
            };

            const res = await fetch('/api/admin/exams', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });

            if (res.ok) {
                toggleModal(false);
                document.getElementById('createExamForm').reset();
                fetchExams();
            } else {
                alert('Gagal menambah paket ujian');
            }
        });

        async function deleteExam(id) {
            if (!confirm('Hapus paket ujian ini beserta seluruh soalnya?')) return;
            const res = await fetch(`/api/admin/exams/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (res.ok) fetchExams();
        }

        function toggleModal(show) {
            document.getElementById('examModal').classList.toggle('hidden', !show);
        }

        fetchExams();
    </script>
</body>
</html>