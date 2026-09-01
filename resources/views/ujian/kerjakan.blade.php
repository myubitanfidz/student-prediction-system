@extends('layouts.app')
@section('title', 'Ujian — Talent Mapping')

@section('content')
<div x-data="examFlow({{ (int) $examId }})" class="min-h-[calc(100vh-4rem)] bg-[#F8F9FA] flex flex-col justify-center py-10 px-4">

    {{-- ==================== SCREEN: LOADING ==================== --}}
    <div x-show="step === 'loading'" class="max-w-md w-full mx-auto text-center space-y-4 py-16">
        <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="text-sm font-semibold text-slate-600">Menyiapkan butir soal ujian...</p>
    </div>

    {{-- ==================== SCREEN 0: KETIKA UJIAN DITUTUP / DILUAR JADWAL ==================== --}}
    <div x-show="step === 'closed'" x-cloak class="max-w-xl w-full mx-auto text-center space-y-6 bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-sm">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
            ⏱
        </div>
        <h2 class="font-display font-black text-2xl text-slate-900" x-text="periodTitle || 'Ujian Belum Tersedia'"></h2>
        <p class="text-sm text-slate-600 leading-relaxed" x-text="lockMessage"></p>
        <div>
            <a href="{{ route('beranda') }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>

    {{-- ==================== SCREEN: SOAL KOSONG ==================== --}}
    <div x-show="step === 'empty'" x-cloak class="max-w-xl w-full mx-auto text-center space-y-6 bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-sm">
        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
            📝
        </div>
        <h2 class="font-display font-black text-2xl text-slate-900">Belum Ada Soal</h2>
        <p class="text-sm text-slate-600 leading-relaxed">Paket ujian ini belum memiliki butir soal yang aktif.</p>
        <div>
            <a href="{{ route('beranda') }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>

    {{-- ==================== SCREEN 1: READY (SUDAH SIAP?) ==================== --}}
    <div x-show="step === 'ready'" x-cloak class="max-w-4xl w-full mx-auto text-center space-y-10">
        <div class="space-y-2">
            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full"
                  x-text="exam?.period_title || 'PSB 2026/2027'"></span>
            <h1 class="font-display font-extrabold text-4xl sm:text-5xl text-slate-900 tracking-tight">
                Sudah siap?
            </h1>
        </div>

        <div class="bg-[#ECEAE4] p-6 sm:p-10 rounded-3xl grid grid-cols-1 md:grid-cols-3 gap-6 shadow-xs">
            <div class="bg-white p-8 rounded-2xl flex items-center justify-center text-center shadow-xs">
                <p class="font-medium text-sm sm:text-base text-slate-800 leading-snug">
                    Tidak ada jawaban yang benar atau salah
                </p>
            </div>
            <div class="bg-white p-8 rounded-2xl flex items-center justify-center text-center shadow-xs">
                <p class="font-medium text-sm sm:text-base text-slate-800 leading-snug">
                    Jujur dengan diri sendiri
                </p>
            </div>
            <div class="bg-white p-8 rounded-2xl flex items-center justify-center text-center shadow-xs">
                <p class="font-medium text-sm sm:text-base text-slate-800 leading-snug">
                    Tiap soal memiliki batas waktu khusus
                </p>
            </div>
        </div>

        <div>
            <button type="button" @click="startExam()"
                    class="bg-[#8C8C8C] hover:bg-[#737373] text-white font-extrabold text-base sm:text-lg px-12 py-3.5 rounded-2xl transition shadow-md active:scale-95">
                Mari kita mulai!
            </button>
        </div>
    </div>

    {{-- ==================== SCREEN 2: PENGERJAAN SOAL + TIMER ==================== --}}
    <div x-show="step === 'exam'" x-cloak class="max-w-4xl w-full mx-auto space-y-6">
        
        {{-- Top Bar Quiz --}}
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Soal:</span>
                <span class="font-mono font-bold text-base sm:text-lg"
                      :class="questionTimeRemaining <= 10 ? 'text-rose-600 animate-pulse' : 'text-slate-900'"
                      x-text="questionTimeRemaining + 's'"></span>
            </div>

            <div class="flex-1 max-w-md space-y-1.5 text-center">
                <p class="text-xs sm:text-sm font-semibold text-slate-700">
                    Aku sudah mengerjakan <span class="font-bold text-slate-900" x-text="`${answeredCount}/${questions.length}`"></span>
                </p>
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-3 rounded-full bg-[#D9D9D9] overflow-hidden">
                        <div class="h-full bg-[#8C8C8C] transition-all duration-300"
                             :style="`width: ${progressPercentage}%`"></div>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-700 shrink-0" x-text="`${progressPercentage}%`"></span>
                </div>
            </div>

            <a href="{{ route('beranda') }}" class="bg-[#D9D9D9] hover:bg-[#C8C8C8] text-slate-800 text-xs sm:text-sm font-bold px-5 py-2 rounded-lg transition">
                Keluar
            </a>
        </div>

        {{-- Waktu Mundur Progress Line --}}
        <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-600 transition-all duration-1000 ease-linear"
                 :style="`width: ${(questionTimeRemaining / Math.max(1, currentQuestionTimeLimit)) * 100}%`"></div>
        </div>

        <div class="text-center">
            <span class="inline-block bg-[#D9D9D9] text-slate-800 font-bold text-xs uppercase tracking-wider px-6 py-1.5 rounded-full"
                  x-text="exam?.title ?? 'QUIZ PENGETAHUAN'"></span>
        </div>

        {{-- Main Box Pertanyaan --}}
        <template x-if="currentQuestion">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-12 shadow-sm space-y-6">
                <div class="flex items-center gap-2">
                    <span class="inline-block bg-[#D9D9D9] text-slate-800 font-semibold text-xs px-4 py-1 rounded-full"
                          x-text="`Pertanyaan ${currentIndex + 1} dari ${questions.length}`"></span>
                    <template x-if="currentQuestion?.gclwama_tag">
                        <span class="inline-block bg-indigo-100 text-indigo-800 font-bold text-xs px-2.5 py-0.5 rounded-full"
                              x-text="`Tag: ${currentQuestion.gclwama_tag}`"></span>
                    </template>
                </div>

                <h2 class="font-display font-bold text-lg sm:text-2xl text-slate-900 leading-relaxed"
                    x-text="currentQuestion?.question_text"></h2>

                {{-- 1. Pilihan Ganda --}}
                <div class="space-y-3 pt-2" x-show="currentQuestion?.type === 'multiple_choice'">
                    <template x-for="(opsi, idx) in (currentQuestion?.options || [])" :key="idx">
                        <label class="flex items-center gap-4 p-4 rounded-xl border border-slate-900/80 cursor-pointer transition-colors"
                               :class="jawaban[currentQuestion.id] === opsi ? 'bg-slate-100 border-slate-950 font-bold' : 'hover:bg-slate-50'">
                            <input type="radio" :name="'q-' + currentQuestion.id" :value="opsi" x-model="jawaban[currentQuestion.id]" class="hidden">
                            <span class="font-bold text-slate-900 text-sm sm:text-base" x-text="String.fromCharCode(65 + idx) + '.'"></span>
                            <span class="text-sm sm:text-base text-slate-800" x-text="opsi"></span>
                        </label>
                    </template>
                </div>

                {{-- 2. Input Esai --}}
                <div x-show="currentQuestion?.type === 'essay'" class="pt-2">
                    <textarea rows="5" placeholder="Tuliskan jawaban atau narasi ceritamu di sini..." x-model="jawaban[currentQuestion.id]"
                              class="w-full rounded-xl border border-slate-900/80 p-4 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"></textarea>
                </div>

                {{-- 3. Input Upload Gambar --}}
                <div x-show="currentQuestion?.type === 'image_upload'" class="pt-2 space-y-4">
                    <label class="block w-full border-2 border-dashed border-slate-400 hover:border-slate-800 rounded-2xl p-8 sm:p-12 text-center cursor-pointer transition bg-slate-50/50 hover:bg-slate-50">
                        <input type="file" accept="image/*" class="hidden" @change="handleImageUpload(currentQuestion.id, $event)">
                        
                        <div class="space-y-2">
                            <svg class="w-10 h-10 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-bold text-sm sm:text-base text-slate-800">
                                Klik atau seret foto/gambar karya kamu ke sini
                            </p>
                            <p class="text-xs text-slate-500">Mendukung JPG, PNG, WEBP (Maks. 10MB)</p>
                        </div>
                    </label>

                    <template x-if="imagePreviews[currentQuestion?.id]">
                        <div class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                            <div class="flex items-center gap-4 min-w-0">
                                <img :src="imagePreviews[currentQuestion.id]" class="w-16 h-16 object-cover rounded-xl border border-emerald-300">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-emerald-900 truncate" x-text="imageFiles[currentQuestion.id]?.name"></p>
                                    <p class="text-[11px] text-emerald-700">Gambar siap dikirim ✓</p>
                                </div>
                            </div>
                            <button type="button" @click="removeUploadedImage(currentQuestion.id)" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-3 py-1.5 rounded-lg border border-rose-200 bg-white">
                                Ganti / Hapus
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Navigasi Bawah --}}
        <div class="flex items-center justify-between pt-2">
            <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                    class="bg-[#D9D9D9] hover:bg-[#C8C8C8] disabled:opacity-40 disabled:cursor-not-allowed text-slate-900 font-bold text-sm sm:text-base px-6 sm:px-8 py-3 rounded-2xl transition">
                &lt; Sebelumnya
            </button>

            <button type="button" x-show="!isLast" @click="nextQuestion()"
                    class="bg-[#D9D9D9] hover:bg-[#C8C8C8] text-slate-900 font-bold text-sm sm:text-base px-6 sm:px-8 py-3 rounded-2xl transition">
                Selanjutnya &gt;
            </button>

            <button type="button" x-show="isLast" @click="finishExam()"
                    class="bg-[#8C8C8C] hover:bg-[#737373] text-white font-bold text-sm sm:text-base px-8 py-3 rounded-2xl transition shadow active:scale-95">
                Selesai &gt;
            </button>
        </div>
    </div>

    {{-- ==================== SCREEN 3: ANALYZING ==================== --}}
    <div x-show="step === 'analyzing'" x-cloak class="max-w-xl w-full mx-auto text-center space-y-8 py-16">
        <div class="space-y-2">
            <h1 class="font-display font-black text-3xl sm:text-4xl text-slate-900">
                Menganalisis bakatmu
            </h1>
            <p class="text-sm sm:text-base text-slate-600">
                Kami sedang memetakan hasil jawaban mu!
            </p>
        </div>

        <div class="w-48 h-48 sm:w-60 sm:h-60 mx-auto rounded-full bg-[#ECEAE4] flex items-center justify-center relative overflow-hidden border border-slate-200">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#8C8C8C_1.5px,transparent_1.5px)] [background-size:12px_12px] animate-pulse"></div>
        </div>

        <div class="max-w-md mx-auto h-3.5 rounded-full bg-[#D9D9D9] overflow-hidden p-0.5">
            <div class="h-full bg-slate-800 rounded-full animate-[progress_2s_ease-in-out_infinite]" style="width: 70%"></div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('examFlow', (examId) => ({
        examId,
        step: 'loading', // 'loading' -> 'ready' | 'exam' | 'analyzing' | 'closed' | 'empty'
        exam: null,
        periodTitle: '',
        lockMessage: '',
        questions: [],
        currentIndex: 0,
        jawaban: {},
        imageFiles: {},
        imagePreviews: {},
        
        questionTimeRemaining: 60,
        currentQuestionTimeLimit: 60,
        timerInterval: null,

        get currentQuestion() {
            if (!this.questions || this.questions.length === 0) return null;
            return this.questions[this.currentIndex] || null;
        },
        get isLast() {
            return this.currentIndex >= (this.questions.length - 1);
        },
        get answeredCount() {
            const textKeys = Object.keys(this.jawaban).filter(k => this.jawaban[k] !== undefined && this.jawaban[k] !== '');
            const imgKeys = Object.keys(this.imageFiles).filter(k => this.imageFiles[k] !== undefined && this.imageFiles[k] !== null);
            return new Set([...textKeys, ...imgKeys]).size;
        },
        get progressPercentage() {
            if (!this.questions.length) return 0;
            return Math.round((this.answeredCount / this.questions.length) * 100);
        },

        async init() {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            try {
                const res = await fetch(`/api/exams/${this.examId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();

                if (res.status === 403) {
                    this.step = 'closed';
                    this.periodTitle = json?.period_title || 'Ujian Terkunci';
                    this.lockMessage = json?.message || 'Ujian belum dapat diakses.';
                    return;
                }

                if (!res.ok) {
                    this.step = 'closed';
                    this.lockMessage = json?.message || 'Terjadi kendala saat memuat ujian.';
                    return;
                }

                this.exam = json?.data?.exam || null;
                this.questions = json?.data?.questions || [];

                if (json?.data?.completed && !json?.data?.retake_allowed) {
                    window.location.href = '/dashboard';
                    return;
                }

                if (this.questions.length === 0) {
                    this.step = 'empty';
                } else {
                    this.step = 'ready';
                }
            } catch (err) {
                console.error(err);
                this.step = 'closed';
                this.lockMessage = 'Gagal terhubung ke server.';
            }
        },

        resetQuestionTimer() {
            clearInterval(this.timerInterval);
            this.currentQuestionTimeLimit = Number(this.currentQuestion?.time_limit_seconds) || 60;
            this.questionTimeRemaining = this.currentQuestionTimeLimit;

            this.timerInterval = setInterval(() => {
                if (this.questionTimeRemaining > 0) {
                    this.questionTimeRemaining--;
                } else {
                    clearInterval(this.timerInterval);
                    if (this.isLast) {
                        this.finishExam();
                    } else {
                        this.nextQuestion();
                    }
                }
            }, 1000);
        },

        handleImageUpload(questionId, event) {
            const file = event.target.files[0];
            if (!file) return;

            this.imageFiles[questionId] = file;
            this.jawaban[questionId] = `[Uploaded: ${file.name}]`;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreviews[questionId] = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeUploadedImage(questionId) {
            delete this.imageFiles[questionId];
            delete this.imagePreviews[questionId];
            delete this.jawaban[questionId];
        },

        startExam() {
            if (!this.questions.length) return;
            this.step = 'exam';
            this.currentIndex = 0;
            this.resetQuestionTimer();
        },

        nextQuestion() {
            if (!this.isLast) {
                this.currentIndex++;
                this.resetQuestionTimer();
            }
        },

        prevQuestion() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.resetQuestionTimer();
            }
        },

        async finishExam() {
            clearInterval(this.timerInterval);
            this.step = 'analyzing';

            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            const formData = new FormData();
            formData.append('exam_id', this.examId);

            let idx = 0;
            for (const q of this.questions) {
                formData.append(`answers[${idx}][question_id]`, q.id);
                formData.append(`answers[${idx}][answer_text]`, this.jawaban[q.id] || '');
                if (this.imageFiles[q.id]) {
                    formData.append(`answers[${idx}][file]`, this.imageFiles[q.id]);
                }
                idx++;
            }

            try {
                await fetch('/api/exams/submit', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (window.notifySuccess) {
                    window.notifySuccess('Ujian selesai & jawaban terkirim!');
                }

                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2000);
            } catch (err) {
                console.error(err);
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            }
        }
    }));
});
</script>
@endsection