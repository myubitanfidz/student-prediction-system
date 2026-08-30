@extends('layouts.app')
@section('title', 'Ujian — Talent Mapping')

@section('content')
<div x-data="examFlow({{ (int) $examId }})" class="min-h-[calc(100vh-4rem)] bg-[#F8F9FA] flex flex-col justify-center py-10 px-4">

    {{-- ==================== SCREEN 1: DESKTOP - 11 (SUDAH SIAP?) ==================== --}}
    <div x-show="step === 'ready'" x-cloak class="max-w-4xl w-full mx-auto text-center space-y-10">
        <h1 class="font-display font-extrabold text-4xl sm:text-5xl text-slate-900 tracking-tight">
            Sudah siap?
        </h1>

        {{-- 3 Instruksi Card Container --}}
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
                    Pilih yang paling menggambarkan dirimu
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

    {{-- ==================== SCREEN 2: DESKTOP - 12 (PENGERJAAN KUIS) ==================== --}}
    <div x-show="step === 'exam'" x-cloak class="max-w-4xl w-full mx-auto space-y-8">
        {{-- Top Bar Quiz --}}
        <div class="flex items-center justify-between gap-4">
            <div class="w-24 sm:w-36 h-9 bg-[#8C8C8C] rounded-lg"></div>

            {{-- Progress Count & Bar --}}
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

        {{-- Badge Kategori Soal --}}
        <div class="text-center">
            <span class="inline-block bg-[#D9D9D9] text-slate-800 font-bold text-xs uppercase tracking-wider px-6 py-1.5 rounded-full"
                  x-text="exam?.title ?? 'QUIZ PENGETAHUAN'"></span>
        </div>

        {{-- Main Quiz Box --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-12 shadow-sm space-y-6">
            <div class="inline-block bg-[#D9D9D9] text-slate-800 font-semibold text-xs px-4 py-1 rounded-full"
                 x-text="`Pertanyaan ${currentIndex + 1}`"></div>

            <h2 class="font-display font-bold text-lg sm:text-2xl text-slate-900 leading-relaxed"
                x-text="currentQuestion?.question_text"></h2>

            {{-- Opsi Pilihan Ganda --}}
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

            {{-- Input Esai jika ada --}}
            <div x-show="currentQuestion?.type === 'essay'" class="pt-2">
                <textarea rows="4" placeholder="Tuliskan jawaban Anda di sini..." x-model="jawaban[currentQuestion.id]"
                          class="w-full rounded-xl border border-slate-900/80 p-4 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"></textarea>
            </div>
        </div>

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

    {{-- ==================== SCREEN 3: DESKTOP - 7 (MENGANALISIS BAKATMU) ==================== --}}
    <div x-show="step === 'analyzing'" x-cloak class="max-w-xl w-full mx-auto text-center space-y-8 py-16">
        <div class="space-y-2">
            <h1 class="font-display font-black text-3xl sm:text-4xl text-slate-900">
                Menganalisis bakatmu
            </h1>
            <p class="text-sm sm:text-base text-slate-600">
                Kami sedang memetakan hasil jawaban mu!
            </p>
        </div>

        {{-- Circular Placeholder Graphic --}}
        <div class="w-48 h-48 sm:w-60 sm:h-60 mx-auto rounded-full bg-[#ECEAE4] flex items-center justify-center relative overflow-hidden border border-slate-200">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#8C8C8C_1.5px,transparent_1.5px)] [background-size:12px_12px] animate-pulse"></div>
        </div>

        {{-- Horizontal Progress Animation --}}
        <div class="max-w-md mx-auto h-3.5 rounded-full bg-[#D9D9D9] overflow-hidden p-0.5">
            <div class="h-full bg-slate-800 rounded-full animate-[progress_2s_ease-in-out_infinite]" style="width: 70%"></div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('examFlow', (examId) => ({
        examId,
        step: 'ready', // 'ready' -> 'exam' -> 'analyzing'
        exam: null,
        questions: [],
        currentIndex: 0,
        jawaban: {},

        get currentQuestion() {
            return this.questions[this.currentIndex] || null;
        },
        get isLast() {
            return this.currentIndex === (this.questions.length - 1);
        },
        get answeredCount() {
            return Object.values(this.jawaban).filter(v => v !== undefined && v !== '').length;
        },
        get progressPercentage() {
            if (!this.questions.length) return 0;
            return Math.round((this.answeredCount / this.questions.length) * 100);
        },

        async init() {
            try {
                const res = await fetch(`/api/exams/${this.examId}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('ts_token')}`,
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                this.exam = json?.data?.exam;
                this.questions = json?.data?.questions || [];
            } catch (err) {
                console.error(err);
            }
        },

        startExam() {
            this.step = 'exam';
        },
        nextQuestion() {
            if (!this.isLast) this.currentIndex++;
        },
        prevQuestion() {
            if (this.currentIndex > 0) this.currentIndex--;
        },

        async finishExam() {
            this.step = 'analyzing';

            const payloadAnswers = Object.entries(this.jawaban).map(([question_id, answer_text]) => ({
                question_id: parseInt(question_id),
                answer_text: answer_text
            }));

            try {
                await fetch('/api/exams/submit', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('ts_token')}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        exam_id: this.examId,
                        answers: payloadAnswers
                    })
                });

                // Tunda sejenak agar animasi loading menganalisis tampil
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2200);
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