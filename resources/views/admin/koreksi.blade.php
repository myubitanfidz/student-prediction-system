@extends('layouts.app')
@section('title', 'Koreksi Jawaban & Karya Santri')

@section('content')
<div x-data="adminKoreksiPage" class="max-w-4xl mx-auto mt-6 sm:mt-8 pb-12 px-4 space-y-6">
    <div x-show="loading" class="text-sm text-slate-500 font-medium">Memuat jawaban santri...</div>
    <div x-show="error" x-text="error" class="text-sm text-rose-700 bg-rose-50 rounded-xl px-4 py-3 border border-rose-200"></div>

    <template x-if="!loading && student">
        <div class="space-y-6">
            {{-- Header Santri & Info Ujian Terpilih --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-900 text-white text-base font-display font-bold flex items-center justify-center shadow-xs shrink-0"
                         x-text="student.name ? student.name.charAt(0).toUpperCase() : 'S'"></div>
                    <div>
                        <h1 class="font-display font-bold text-xl text-slate-900" x-text="student.name"></h1>
                        <p class="text-xs text-slate-500" x-text="student.email"></p>
                        <template x-if="selectedExam">
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200" x-text="selectedExam.period_title"></span>
                                <span class="text-xs font-bold text-slate-800" x-text="selectedExam.title"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-4 py-2 rounded-xl transition inline-flex items-center gap-1 self-start sm:self-center">
                    ← Kembali ke Dashboard
                </a>
            </div>

            {{-- Daftar Jawaban Santri --}}
            <template x-if="answers.length > 0">
                <div class="space-y-4">
                    <template x-for="(answer, idx) in answers" :key="answer.answer_id">
                        <article class="p-6 space-y-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
                            {{-- Header Pertanyaan --}}
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600" x-text="answer.exam_title || 'Ujian Santri'"></p>
                                    <h3 class="font-display font-bold text-base text-slate-900" x-text="answer.question_text || `Butir Soal #${idx + 1}`"></h3>
                                    
                                    {{-- Badge Tipe Soal & Status AI --}}
                                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                              :class="{
                                                  'bg-blue-100 text-blue-800': String(answer.question_type).toLowerCase() === 'multiple_choice',
                                                  'bg-emerald-100 text-emerald-800': String(answer.question_type).toLowerCase() === 'image_upload',
                                                  'bg-purple-100 text-purple-800': String(answer.question_type).toLowerCase() !== 'multiple_choice' && String(answer.question_type).toLowerCase() !== 'image_upload'
                                              }"
                                              x-text="String(answer.question_type).toLowerCase() === 'multiple_choice' ? 'Pilihan Ganda' : (String(answer.question_type).toLowerCase() === 'image_upload' ? 'Upload Karya Gambar' : 'Esai')"></span>
                                        
                                        <template x-if="answer.is_auto_graded">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-600 text-white tracking-wide">
                                                <span>⚡ AI Graded</span>
                                            </span>
                                        </template>

                                        <template x-if="answer.gclwama_tag">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700"
                                                  x-text="`Bagian ${answer.gclwama_tag}`"></span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Status Nilai Saat Ini --}}
                                <div class="text-right shrink-0">
                                    <template x-if="answer.current_score !== null && answer.current_score !== undefined">
                                        <div>
                                            <p class="font-mono text-2xl font-black text-emerald-600" x-text="answer.current_score + '%'"></p>
                                            <p class="text-[10px] uppercase font-bold text-slate-400" x-text="answer.is_auto_graded ? 'Skor Otomatis AI' : 'Telah Dinilai'"></p>
                                        </div>
                                    </template>
                                    <template x-if="answer.current_score === null || answer.current_score === undefined">
                                        <span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                            Belum Dinilai
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- Penampil Gambar Karya --}}
                            <template x-if="answer.file_url">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                                    <p class="text-xs font-bold text-slate-700">Hasil Karya Gambar / Foto yang Diunggah:</p>
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                        <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-300 bg-white"
                                             @click="previewModalImg = answer.file_url">
                                            <img :src="answer.file_url" 
                                                 class="h-44 w-auto max-w-full object-contain rounded-xl hover:scale-105 transition duration-300">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">
                                                🔍 Klik untuk Memperbesar
                                            </div>
                                        </div>
                                        <div class="space-y-1 text-xs">
                                            <a :href="answer.file_url" target="_blank" class="text-indigo-600 font-bold hover:underline flex items-center gap-1">
                                                <span>Buka di tab baru</span> ↗
                                            </a>
                                            <p class="text-slate-400 text-[11px]">Berkas lampiran santri.</p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Teks Jawaban Santri & Kunci Referensi AI --}}
                            <div class="space-y-3" x-show="answer.student_answer">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">Jawaban Santri:</p>
                                    <p class="text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-wrap" x-text="answer.student_answer"></p>
                                </div>

                                <template x-if="answer.correct_answer">
                                    <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-xl p-4 space-y-1">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-900">Kunci / Referensi AI:</p>
                                        <p class="text-xs text-indigo-950 leading-relaxed font-medium" x-text="answer.correct_answer"></p>
                                    </div>
                                </template>
                            </div>

                            {{-- Form Input Nilai & Tombol Aksi --}}
                            <template x-if="String(answer.question_type).toLowerCase() !== 'multiple_choice'">
                                <div class="flex items-center justify-between flex-wrap gap-3 pt-3 border-t border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <label :for="'score-input-' + answer.answer_id" class="text-xs font-bold uppercase text-slate-600">
                                            <span x-text="answer.is_auto_graded ? 'Ubah / Sesuaikan Nilai:' : 'Beri Nilai (0–100):'"></span>
                                        </label>
                                        <input type="number" 
                                               :id="'score-input-' + answer.answer_id" 
                                               :name="'score_' + answer.answer_id"
                                               min="0" max="100" 
                                               x-model="scores[answer.answer_id]"
                                               placeholder="0-100"
                                               class="w-24 rounded-xl border border-slate-300 p-2 text-sm font-mono font-bold text-center focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        {{-- Tombol Pemicu Koreksi AI --}}
                                        <template x-if="answer.question_type === 'essay' && answer.correct_answer">
                                            <button type="button" @click="regradeAi(answer.answer_id)"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs active:scale-95 transition flex items-center gap-1.5">
                                                <span>⚡ Nilai AI</span>
                                            </button>
                                        </template>

                                        {{-- Tombol Simpan Manual --}}
                                        <button type="button" @click="saveScore(answer.answer_id)"
                                                class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs active:scale-95 transition flex items-center gap-1.5">
                                            <span x-text="answer.is_auto_graded ? 'Perbarui Nilai' : 'Simpan Nilai'"></span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </article>
                    </template>
                </div>
            </template>

            <div x-show="answers.length === 0" class="p-8 text-center text-sm text-slate-400 bg-white rounded-2xl border border-slate-200">
                Santri ini belum memiliki jawaban yang perlu dikoreksi pada ujian ini.
            </div>
        </div>
    </template>

    {{-- Lightbox Zoom Image Modal --}}
    <div x-show="previewModalImg" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs"
         @click="previewModalImg = null">
        <div class="relative max-w-4xl max-h-[90vh] bg-white p-2 rounded-2xl shadow-2xl overflow-hidden" @click.stop>
            <button type="button" @click="previewModalImg = null" class="absolute top-4 right-4 bg-slate-900/70 hover:bg-slate-900 text-white rounded-full p-2 text-xs font-bold">
                ✕
            </button>
            <img :src="previewModalImg" class="max-h-[80vh] w-auto mx-auto object-contain rounded-xl" alt="Preview Karya">
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminKoreksiPage', () => ({
        loading: true,
        error: null,
        student: null,
        selectedExam: null,
        answers: [],
        scores: {},
        previewModalImg: null,
        userId: '',

        async init() {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            const urlParams = new URLSearchParams(window.location.search);
            const examId = urlParams.get('exam_id') || '';

            const pathParts = window.location.pathname.split('/').filter(Boolean);
            this.userId = pathParts[pathParts.length - 1];

            try {
                const res = await fetch(`/api/admin/students/${this.userId}/answers?exam_id=${examId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal memuat jawaban santri.');

                this.student = json?.data?.student || null;
                this.answers = json?.data?.answers || [];

                this.answers.forEach(a => {
                    if (a.current_score !== null && a.current_score !== undefined) {
                        this.scores[a.answer_id] = a.current_score;
                    }
                });

                if (this.answers.length > 0) {
                    this.selectedExam = {
                        title: this.answers[0].exam_title,
                        period_title: 'Ujian Santri'
                    };
                }
            } catch (e) {
                console.error("Error koreksi:", e);
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async regradeAi(answerId) {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            try {
                const res = await fetch('/api/admin/regrade-ai', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ answer_id: answerId })
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal memproses penilaian AI.');

                const target = this.answers.find(a => a.answer_id === answerId);
                if (target) {
                    target.current_score = json.score;
                    target.is_auto_graded = true;
                    this.scores[answerId] = json.score;
                }
                alert(json.message);
            } catch (e) {
                alert(e.message || 'Terjadi kesalahan pada penilaian AI.');
            }
        },

        async saveScore(answerId) {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            const scoreVal = this.scores[answerId];

            if (scoreVal === undefined || scoreVal === '' || isNaN(scoreVal)) {
                alert('Silakan masukkan nilai antara 0 sampai 100.');
                return;
            }

            try {
                const res = await fetch('/api/admin/grade', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        answer_id: answerId,
                        score: parseInt(scoreVal)
                    })
                });

                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal menyimpan nilai.');

                const target = this.answers.find(a => a.answer_id === answerId);
                if (target) {
                    target.current_score = parseInt(scoreVal);
                    target.is_auto_graded = false;
                }

                alert('Nilai berhasil disimpan!');
            } catch (e) {
                alert(e.message || 'Terjadi kesalahan saat menyimpan nilai.');
            }
        }
    }));
});
</script>
@endsection