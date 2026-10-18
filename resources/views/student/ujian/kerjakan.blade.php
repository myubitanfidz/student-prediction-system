@extends('layouts.app')
@section('title', 'Ujian — Talent Mapping')

@section('content')
<div x-data="examFlow('{{ $examId }}')" 
     @contextmenu.prevent 
     class="min-h-[calc(100vh-4rem)] bg-[#F8F9FA] flex flex-col justify-center py-10 px-4 select-none">

    {{-- ==================== SCREEN: LOADING ==================== --}}
    <div x-show="step === 'loading'" class="max-w-md w-full mx-auto text-center space-y-4 py-16">
        <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="text-sm font-semibold text-slate-600">Menyiapkan butir soal ujian...</p>
    </div>

    {{-- ==================== MODAL WARNING: PERINGATAN CURANG PINDAH TAB ==================== --}}
    <div x-show="showWarningModal" x-cloak class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 text-center space-y-6 shadow-2xl border border-rose-100">
            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto text-3xl font-black animate-bounce">
                ⚠️
            </div>
            <div class="space-y-2">
                <h3 class="text-xl font-extrabold text-slate-900">Peringatan Kecurangan!</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Anda terdeteksi meninggalkan halaman ujian / berpindah tab. 
                </p>
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 inline-block">
                    <p class="text-xs font-bold text-rose-700">
                        Pelanggaran: <span class="text-base" x-text="violations"></span> / <span x-text="maxViolations"></span>
                    </p>
                </div>
                <p class="text-[11px] text-slate-500">
                    Jika mencapai batas maksimal, ujian akan otomatis diselesaikan dan dilaporkan ke pengawas.
                </p>
            </div>
            <button type="button" @click="closeWarningModal()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl transition shadow-md active:scale-95 text-sm">
                Saya Mengerti &amp; Kembali Mengerjakan
            </button>
        </div>
    </div>

    {{-- ==================== MODAL: SELESAIKAN QUIZ ==================== --}}
<div x-show="showFinishModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
    <div @click.outside="showFinishModal = false" class="bg-white rounded-3xl max-w-sm w-full p-8 text-center space-y-5 shadow-2xl border border-slate-100 relative">
        
        {{-- Custom Icon --}}
        <div class="w-16 h-16 bg-[#FBBF24] rounded-full flex items-center justify-center mx-auto text-3xl shadow-sm">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h3 class="font-display font-black text-2xl text-[#E85D4E]">Selesaikan Quiz?</h3>
        
        <p class="text-sm text-slate-500 leading-relaxed">
            Pastikan kamu sudah memeriksa kembali seluruh jawaban sebelum menyelesaikan quiz.
        </p>

        {{-- Info Box --}}
        <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 inline-block w-full">
            <p class="text-sm font-bold text-slate-400">
                <span x-text="answeredCount"></span>/<span x-text="questions.length"></span> quiz telah dijawab
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3 pt-2">
            <button type="button" @click="showFinishModal = false" class="flex-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-3 rounded-xl transition text-sm">
                Kembali
            </button>
            <button type="button" @click="finishExam()" class="flex-1 bg-[#6C70EB] hover:bg-[#5B50E5] text-white font-bold py-3 rounded-xl transition text-sm shadow-md active:scale-95">
                Selesai
            </button>
        </div>
    </div>
</div>

    {{-- ==================== SCREEN 0: KETIKA UJIAN DITUTUP / DILUAR JADWAL ==================== --}}
    <div x-show="step === 'closed'" x-cloak class="max-w-xl w-full mx-auto text-center space-y-6 bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-sm">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">⏱</div>
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
        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">📝</div>
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
                    Dilarang berpindah tab atau membuka aplikasi lain
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

       {{-- ==================== SCREEN 2: PENGERJAAN SOAL ==================== --}}
    <div x-show="step === 'exam'" x-cloak class="flex flex-col h-full w-full max-w-7xl mx-auto pt-6">
        
        {{-- MAIN CONTENT (No more top white header! Progress bar is now in the layout topbar) --}}
        <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-5 gap-6 w-full">
            
            {{-- LEFT: QUESTION AREA --}}
            <div class="lg:col-span-3 space-y-4">
                
                {{-- Banner --}}
                <div class="bg-[#FF7B42] text-white font-bold py-3 px-6 rounded-t-xl text-center text-sm shadow-sm"
                     x-text="exam?.title ?? 'QUIZ PENGETAHUAN'">
                </div>

                {{-- Question Card --}}
                <div class="bg-white p-5 sm:p-8 rounded-b-xl shadow-sm space-y-6 min-h-[400px] flex flex-col">
                    <template x-if="currentQuestion">
                        <div class="flex-1 flex flex-col">
                            
                            {{-- Header Row (Now only shows the "PERTANYAAN X DARI Y" and Timer) --}}
                            <div class="flex items-center justify-between mb-5">
                                <div class="inline-block bg-[#3B82F6] text-white text-[11px] font-bold px-4 py-1.5 rounded-full tracking-wider uppercase">
                                    PERTANYAAN <span x-text="currentIndex + 1"></span> DARI <span x-text="questions.length"></span>
                                </div>
                                <div class="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-full">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Waktu:</span>
                                    <span class="font-mono font-bold text-sm" 
                                          :class="questionTimeRemaining <= 10 ? 'text-rose-600 animate-pulse' : 'text-slate-800'" 
                                          x-text="questionTimeRemaining + 's'"></span>
                                </div>
                            </div>

                            {{-- Question Text --}}
                            <h2 class="font-bold text-lg sm:text-xl text-slate-800 leading-snug mb-6" x-text="currentQuestion?.question_text"></h2>

                            {{-- 1. Multiple Choice --}}
                            <div class="space-y-3" x-show="currentQuestion?.type === 'multiple_choice'">
                                <template x-for="(opsi, idx) in (currentQuestion?.options || [])" :key="idx">
                                    <label class="flex items-center gap-4 p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-200"
                                           :class="jawaban[currentQuestion.id] === opsi.token ? 'border-[#3B82F6] bg-[#EFF6FF] shadow-xs' : 'border-slate-200 hover:border-[#3B82F6] hover:bg-slate-50'">
                                        <input type="radio" :name="'q-' + currentQuestion.id" :value="opsi.token" x-model="jawaban[currentQuestion.id]" class="hidden">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                                             :class="jawaban[currentQuestion.id] === opsi.token ? 'border-[#3B82F6]' : 'border-slate-300'">
                                            <div class="w-2.5 h-2.5 rounded-full bg-[#3B82F6]" x-show="jawaban[currentQuestion.id] === opsi.token"></div>
                                        </div>
                                        <span class="text-sm font-medium" 
                                              :class="jawaban[currentQuestion.id] === opsi.token ? 'text-[#1E3A8A]' : 'text-slate-700'" 
                                              x-text="opsi.text"></span>
                                    </label>
                                </template>
                            </div>

                            {{-- 2. Essay --}}
                            <div x-show="currentQuestion?.type === 'essay'" class="pt-2">
                                <textarea rows="4" placeholder="Tuliskan jawaban atau narasi ceritamu di sini..." x-model="jawaban[currentQuestion.id]"
                                          class="w-full rounded-xl border border-slate-300 p-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            {{-- 3. Image Upload --}}
                            <div x-show="currentQuestion?.type === 'image_upload'" class="pt-2 space-y-4">
                                <label class="block w-full border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 sm:p-10 text-center cursor-pointer transition bg-slate-50/50 hover:bg-blue-50/30">
                                    <input type="file" accept="image/*" class="hidden" 
                                           @click="prepareFilePicker()"
                                           @cancel="resetFilePicker()"
                                           @change="handleImageUpload(currentQuestion.id, $event)">
                                    <div class="space-y-2">
                                        <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="font-bold text-xs sm:text-sm text-slate-800">Klik atau seret foto/gambar karya kamu ke sini</p>
                                        <p class="text-[11px] text-slate-500">Mendukung JPG, PNG, WEBP, PDF (Maks. 5MB)</p>
                                    </div>
                                </label>
                                <template x-if="imagePreviews[currentQuestion?.id]">
                                    <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-200 rounded-2xl">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img :src="imagePreviews[currentQuestion.id]" class="w-12 h-12 object-cover rounded-xl border border-emerald-300">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-emerald-900 truncate" x-text="imageFiles[currentQuestion.id]?.name"></p>
                                                <p class="text-[10px] text-emerald-700">Gambar siap dikirim ✓</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeUploadedImage(currentQuestion.id)" class="text-rose-600 hover:text-rose-800 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-rose-200 bg-white">
                                            Ganti / Hapus
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Bottom Navigation (SMALLER BUTTONS) --}}
                    <div class="flex items-center justify-between pt-5 border-t border-slate-100 mt-auto">
                        <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                                class="bg-white border border-slate-300 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 font-bold text-xs px-4 py-2 rounded-lg transition">
                            ← Sebelumnya
                        </button>

                        <button type="button" x-show="!isLast" @click="nextQuestion()"
                                class="bg-[#3B82F6] hover:bg-blue-600 text-white font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm">
                            Selanjutnya →
                        </button>

                        <button type="button" x-show="isLast" @click="finishExam()"
                                class="bg-[#10B981] hover:bg-emerald-600 text-white font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm">
                            Selesai →
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT: SIDEBAR (WIDER) --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 sticky top-20 space-y-5">
                    
                    <h3 class="font-bold text-center text-slate-700 text-sm">Navigasi Soal</h3>
                    
                    {{-- Number Grid --}}
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="(q, idx) in questions" :key="idx">
                            <button @click="currentIndex = idx; resetQuestionTimer();"
                                    class="h-9 w-full rounded-md font-bold text-xs flex items-center justify-center transition"
                                    :class="{
                                        'bg-[#10B981] text-white shadow-sm': jawaban[q.id] !== undefined && jawaban[q.id] !== '',
                                        'bg-[#EF4444] text-white shadow-md ring-2 ring-rose-200': currentIndex === idx,
                                        'bg-slate-200 text-slate-600 hover:bg-slate-300': (jawaban[q.id] === undefined || jawaban[q.id] === '') && currentIndex !== idx
                                    }">
                                <span x-text="idx + 1"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Legend --}}
                    <div class="text-[11px] space-y-2 pt-3 border-t border-slate-100 text-slate-600 font-medium">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-[#10B981] rounded-sm"></div> Sudah Dijawab
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-[#EF4444] rounded-sm"></div> Sedang Dikerjakan
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-slate-200 rounded-sm"></div> Belum Dikerjakan
                        </div>
                    </div>

                    {{-- Finish Button --}}
                    <button type="button" @click="if(answeredCount === questions.length) showFinishModal = true;" 
                            class="w-full font-bold py-3.5 rounded-xl transition text-sm mt-4 shadow-sm active:scale-95"
                            :class="answeredCount === questions.length && questions.length > 0 ? 'bg-[#FBBF24] hover:bg-[#F59E0B] text-white cursor-pointer' : 'bg-[#E5E7EB] text-slate-400 cursor-not-allowed'">
                        SELESAI!
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== SCREEN 3: ANALYZING ==================== --}}
    <div x-show="step === 'analyzing'" x-cloak class="max-w-xl w-full mx-auto text-center space-y-8 py-16">
        <div class="space-y-2">
            <h1 class="font-display font-black text-3xl sm:text-4xl text-slate-900">Menganalisis bakatmu</h1>
            <p class="text-sm sm:text-base text-slate-600">Kami sedang memetakan hasil jawaban mu!</p>
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
        step: 'loading',
        exam: null,
        periodTitle: '',
        lockMessage: '',
        questions: [],
        currentIndex: 0,
        sessionNonce: '',
        jawaban: {},
        imageFiles: {},
        imagePreviews: {},
        
        violations: 0,
        maxViolations: 3,
        showWarningModal: false,
        showFinishModal: false,
        isPickingFile: false,

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
            this.setupKeyGuards();

             // 🌟 THIS IS THE MISSING PIECE — Syncs exam state to topbar
    Alpine.effect(() => {
        const store = Alpine.store('examHeader');
        if (store) {
            store.active  = this.step === 'exam';
            store.answered = this.answeredCount;
            store.total    = this.questions.length;
            store.percent  = this.progressPercentage;
        }
    });


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
                this.sessionNonce = this.exam?.session_nonce || '';
                this.questions = json?.data?.questions || [];

                // 🌟 Arahkan langsung ke halaman total skor jika ujian telah diselesaikan
                if (json?.data?.completed && !json?.data?.retake_allowed) {
                    window.location.href = `/hasil/${this.examId}`;
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

        setupAntiCheatListeners() {
            document.addEventListener('visibilitychange', () => {
                if (this.step === 'exam' && document.hidden) {
                    if (this.isPickingFile) return;
                    this.handleViolation();
                }
            });

            window.addEventListener('blur', () => {
                if (this.step === 'exam') {
                    if (this.isPickingFile) return;
                    this.handleViolation();
                }
            });
        },

        setupKeyGuards() {
            window.addEventListener('keydown', (e) => {
                if (
                    e.key === 'F12' || 
                    (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key.toUpperCase())) ||
                    (e.ctrlKey && ['U', 'C', 'V', 'S'].includes(e.key.toUpperCase()))
                ) {
                    e.preventDefault();
                    return false;
                }
            });
        },

        prepareFilePicker() {
            this.isPickingFile = true;
        },

        resetFilePicker() {
            setTimeout(() => {
                this.isPickingFile = false;
            }, 500);
        },

        handleViolation() {
            if (this.step !== 'exam') return;
            this.violations++;

            if (this.violations >= this.maxViolations) {
                alert('Anda telah melebihi batas perpindahan tab (3 kali). Ujian Anda otomatis dikumpulkan!');
                this.finishExam();
            } else {
                this.showWarningModal = true;
            }
        },

        closeWarningModal() {
            this.showWarningModal = false;
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
            this.resetFilePicker();

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
            this.setupAntiCheatListeners();
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
            formData.append('session_nonce', this.sessionNonce);
            formData.append('violation_count', this.violations);
            
            let idx = 0;
            for (const q of this.questions) {
                formData.append(`answers[${idx}][question_id]`, q.id);
                formData.append(`answers[${idx}][answer_text]`, this.jawaban[q.id] || '');
                
                if (this.imageFiles[q.id] instanceof File) {
                    formData.append(`answers[${idx}][file]`, this.imageFiles[q.id]);
                }
                idx++;
            }

            try {
                const res = await fetch('/api/exams/submit', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!res.ok) {
                    const errData = await res.json().catch(() => ({}));
                    console.error('Submit error:', errData);
                }

                if (window.notifySuccess) {
                    window.notifySuccess('Ujian selesai & jawaban terkirim!');
                }

                // 🌟 Redirect langsung ke detail hasil nilai ujian ini
                setTimeout(() => {
                    window.location.href = `/hasil/${this.examId}`;
                }, 2000);
            } catch (err) {
                console.error(err);
                setTimeout(() => {
                    window.location.href = `/hasil/${this.examId}`;
                }, 1500);
            }
        }
    }));
});
</script>
@endsection