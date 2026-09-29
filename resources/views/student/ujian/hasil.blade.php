@extends('layouts.app')
@section('title', 'Total Skor Ujian — Talent Mapping')

@section('content')
<div x-data="hasilDetailPage('{{ $examId }}')" class="min-h-[calc(100vh-4rem)] bg-[#FFFDF0] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg mx-auto space-y-4" x-show="!loading" x-cloak>

        {{-- ==================== HEADER ==================== --}}
        <div class="text-center mb-3">
            <img src="{{ asset('images/landing/yay.png') }}" alt="" aria-hidden="true" class="h-auto w-44 sm:w-70 mx-auto">
        </div>

        <h1 class="font-display font-black text-2xl sm:text-3xl text-slate-900 text-center">
            Pengerjaan selesai!
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 text-center">
            kamu berhasil menuntaskan tes Talent Mapping
        </p>

        {{-- ==================== MAIN CARD ==================== --}}
        <div class="bg-[#F8F9FA] rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4">

            {{-- Top Row: Bidang + Overall Score --}}
            <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Minat kamu bakat dibidang:
                    </p>
                    <h2 class="font-display font-black text-lg sm:text-xl text-[#5B50E5]"
                        x-text="examTitle">
                    </h2>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Skor Keseluruhan
                    </p>
                    <p class="font-display font-black text-3xl sm:text-4xl text-[#E85D4E] font-mono"
                       x-text="totalScore">
                    </p>
                </div>
            </div>

            {{-- Middle Row: Mascot + Rincian Skor --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center pt-2">

                {{-- Star Mascot --}}
                <div class="flex items-center justify-center h-28 sm:h-32">
                    <img src="{{ asset('images/landing/star.svg') }}" alt="" aria-hidden="true" class="h-full w-auto max-w-full object-contain">
                </div>

                {{-- Rincian Skor (1 bar only) --}}
                <div class="bg-white rounded-2xl p-4 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display font-bold text-xs text-slate-900"
                            x-text="isItExam ? 'Rincian Skor (GCLWAMA)' : 'Rincian Skor Ujian'"></h3>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider"
                              x-text="isItExam ? 'Global' : 'Per Ujian'"></span>
                    </div>

                    {{-- ONE single bar: overall score --}}
                    <div class="space-y-1.5 pt-1">
                        <div class="flex justify-between items-center text-[11px] font-bold">
                            <span class="text-slate-700"
                                  x-text="isItExam ? 'Skor GCLWAMA' : examTitle"></span>
                            <span class="text-slate-900 font-mono text-sm"
                                  x-text="`${displayScore}%`"></span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700"
                                 :style="`width: ${displayScore}%; background-color: #5B50E5`">
                            </div>
                        </div>
                        <p class="text-[9px] text-slate-400 italic pt-0.5"
                           x-text="isItExam ? 'Rata-rata dari 7 dimensi GCLWAMA' : 'Hasil dari jawaban pilihan ganda kamu'">
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== PREDIKSI BAKAT (simplified) ==================== --}}
       {{-- ==================== PREDIKSI BAKAT (with 4 bars, title = GCLWAMA) ==================== --}}
<div x-show="isItExam && gclwamaScore > 0" x-cloak
     class="bg-gradient-to-br from-[#5B50E5] to-[#7C70F0] rounded-3xl p-5 text-white shadow-md space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-white/70">
                Prediksi Bakat Terbaik Kamu
            </p>
            <h3 class="font-display font-black text-2xl mt-1">GCLWAMA</h3>
        </div>
        <div class="text-right">
            <p class="font-display font-black text-3xl font-mono" x-text="`${gclwamaScore}%`"></p>
            <p class="text-[10px] font-bold text-white/70">Match Score</p>
        </div>
    </div>
    <div class="space-y-2 pt-2 border-t border-white/20">
        <template x-for="(score, role) in careerPredictions" :key="role">
            <div class="space-y-1">
                <div class="flex justify-between items-center text-[10px] font-bold">
                    <span x-text="role"></span>
                    <span class="font-mono" x-text="`${score}%`"></span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-white/20 overflow-hidden">
                    <div class="h-full rounded-full bg-white transition-all duration-700"
                         :style="`width: ${score}%`"></div>
                </div>
            </div>
        </template>
    </div>
</div>

        {{-- ==================== DESCRIPTION TEXT ==================== --}}
        <div class="text-center px-2">
            <p class="text-xs text-slate-600 leading-relaxed" x-text="feedbackText"></p>
        </div>

        {{-- ==================== STAT BOXES ==================== --}}
        <div class="grid grid-cols-3 gap-2">
            <div class="rounded-2xl p-3 text-center" style="background-color: #E9E5FF;">
                <p class="font-display font-black text-xl text-[#5B50E5] font-mono"
                   x-text="`${totalScore}%`">
                </p>
                <p class="text-[9px] font-bold text-slate-600 mt-1">Skor benar</p>
            </div>

            <div class="rounded-2xl p-3 text-center" style="background-color: #D1FAE5;">
                <p class="font-display font-black text-xl text-[#10B981] font-mono"
                   x-text="correctCount">
                </p>
                <p class="text-[9px] font-bold text-slate-600 mt-1">Benar</p>
            </div>

            <div class="rounded-2xl p-3 text-center" style="background-color: #FEE2E2;">
                <p class="font-display font-black text-xl text-[#EF4444] font-mono"
                   x-text="wrongCount">
                </p>
                <p class="text-[9px] font-bold text-slate-600 mt-1">Salah</p>
            </div>
        </div>

        {{-- ==================== UPLOAD PORTOFOLIO ==================== --}}
        <div class="flex flex-col items-center gap-3 pt-2 pb-4">
            <a href="{{ route('portofolio.index') }}"
               class="group relative w-full max-w-[280px] h-24 rounded-2xl border-2 border-[#5B50E5] bg-[#5B50E5]/10 hover:bg-[#5B50E5]/20 transition-all flex flex-col items-center justify-center gap-1 cursor-pointer active:scale-95">
                <svg class="w-7 h-7 text-[#5B50E5] group-hover:scale-110 transition-transform"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-[9px] font-bold text-[#5B50E5] uppercase tracking-wider">Upload Portofolio</span>
            </a>
        </div>

    </div>

    {{-- Loading Spinner --}}
    <div x-show="loading" class="py-32 text-center space-y-3">
        <div class="w-8 h-8 border-4 border-slate-900 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="text-xs font-semibold text-slate-500">Menghitung total skor...</p>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('hasilDetailPage', (examId) => ({
        // ============ Properties ============
        examId,
        loading: true,
        examTitle: 'Ujian Talent Mapping',
        examCategory: 'Umum',
        totalScore: 0,
        correctCount: 0,
        wrongCount: 0,
        feedbackText: 'Memuat analisis...',
        isItExam: false,
        gclwamaScore: 0,
        displayScore: 0,
        careerPredictions: {},   // 🌟 added here, next to the other props

        // ============ Methods ============
        async init() {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            try {
                const res = await fetch('/api/dashboard', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                const allStats = json?.data?.exam_stats || [];
                const student  = json?.data?.student || {};

                // 🎯 4 career predictions
                this.careerPredictions = student.career_predictions || {};

                // 🎯 Match the current exam
                const current = allStats.find(s => String(s.hash_id) === String(this.examId))
                             || allStats.find(s => String(s.exam_id) === String(this.examId))
                             || allStats[0];

                if (current) {
                    this.examTitle    = current.exam_title || 'Ujian Talent Mapping';
                    this.examCategory = current.category || current.subcategory || 'Umum';
                    this.totalScore   = Math.round(current.mc_accuracy_pct || 0);
                    this.correctCount = current.mc_correct_count ?? 0;
                    this.wrongCount   = current.mc_wrong_count   ?? 0;

                    const catText = [
                        current.category || '',
                        current.subcategory || '',
                        this.examTitle || ''
                    ].join(' ').toLowerCase();

                    const isBahasa = catText.includes('bahasa')
                                  || catText.includes('arab')
                                  || catText.includes('inggris')
                                  || catText.includes('english');

                    this.isItExam = !isBahasa;
                }

                // 📊 GCLWAMA overall average
                const gcl = student.gclwama_breakdown || {};
                const gclValues = [
                    gcl['Gambar (G)']     ?? 0,
                    gcl['Cerita (C)']     ?? 0,
                    gcl['Layout (L)']     ?? 0,
                    gcl['Warna (W)']      ?? 0,
                    gcl['Animasi (A)']    ?? 0,
                    gcl['Matematika (M)'] ?? 0,
                    gcl['Algoritma (A)']  ?? 0,
                ].map(v => Number(v) || 0);

                const nonZero = gclValues.filter(v => v > 0);
                this.gclwamaScore = nonZero.length > 0
                    ? Math.round(nonZero.reduce((a, b) => a + b, 0) / nonZero.length)
                    : 0;

                this.displayScore = this.isItExam ? this.gclwamaScore : this.totalScore;

                // ✍️ Feedback text
                if (this.isItExam) {
                    if (this.gclwamaScore >= 70) {
                        this.feedbackText = `Kamu memiliki jiwa visual dan estetika yang baik untuk dipadukan dengan teknis. Kamu juga mampu memahami dan menghasilkan sebuah karya visual, sehingga kamu berbakat! Bidang ${this.examTitle} adalah tempat terbentuk kamu untuk menyalurkan bakat yang kamu miliki.`;
                    } else if (this.gclwamaScore >= 40) {
                        this.feedbackText = `Kamu sudah memiliki dasar yang cukup baik di bidang ${this.examTitle}. Teruslah berlatih untuk mengasah kemampuan visual dan teknis kamu, sehingga bakat tersebut dapat berkembang maksimal.`;
                    } else {
                        this.feedbackText = `Bakat kamu di bidang ${this.examTitle} masih bisa terus dikembangkan. Jangan berkecil hati, teruslah belajar dan berlatih untuk menemukan gaya unik kamu sendiri!`;
                    }
                } else {
                    if (this.totalScore >= 70) {
                        this.feedbackText = `Kemampuan ${this.examTitle} kamu sangat baik! Terus asah kemampuan berbahasa kamu dengan membaca dan berlatih secara rutin, agar bisa bersaing di kancah global.`;
                    } else if (this.totalScore >= 40) {
                        this.feedbackText = `Kamu sudah punya dasar ${this.examTitle} yang cukup baik. Perbanyak latihan kosakata dan percakapan supaya kemampuan kamu terus meningkat!`;
                    } else {
                        this.feedbackText = `Kemampuan ${this.examTitle} kamu masih bisa terus ditingkatkan. Jangan menyerah, teruslah berlatih setiap hari dan kamu akan melihat kemajuan besar!`;
                    }
                }
            } catch (err) {
                console.error(err);
                this.feedbackText = 'Terjadi kendala saat memuat analisis skor.';
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection