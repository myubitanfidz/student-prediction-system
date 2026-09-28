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

                {{-- Rincian Skor (GCLWAMA) --}}
                <div class="bg-white rounded-2xl p-4 border border-slate-200 space-y-2.5">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-display font-bold text-xs text-slate-900">Rincian Skor (GCLWAMA)</h3>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Global</span>
                    </div>

                    <template x-if="hasBreakdown">
                        <div class="space-y-2.5">
                            <template x-for="detail in breakdown" :key="detail.label">
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                        <span class="text-slate-700" x-text="detail.label"></span>
                                        <span class="text-slate-900 font-mono" x-text="`${detail.value}%`"></span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-700"
                                             :style="`width: ${detail.value}%; background-color: ${detail.color}`">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Bahasa exams have no GCLWAMA tags --}}
                    <template x-if="!hasBreakdown">
                        <p class="text-[11px] text-slate-400 italic text-center py-4">
                            Belum ada data GCLWAMA untuk ujian ini.
                        </p>
                    </template>
                </div>
            </div>
        </div>

        {{-- ==================== PREDIKSI BAKAT (CAREER) ==================== --}}
        <div x-show="topInclination" x-cloak
             class="bg-gradient-to-br from-[#5B50E5] to-[#7C70F0] rounded-3xl p-5 text-white shadow-md space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/70">
                        Prediksi Bakat Terbaik Kamu
                    </p>
                    <h3 class="font-display font-black text-2xl mt-1" x-text="topInclination"></h3>
                </div>
                <div class="text-right">
                    <p class="font-display font-black text-3xl font-mono" x-text="`${topScore}%`"></p>
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
            {{-- Skor Benar --}}
            <div class="rounded-2xl p-3 text-center" style="background-color: #E9E5FF;">
                <p class="font-display font-black text-xl text-[#5B50E5] font-mono"
                   x-text="`${totalScore}%`">
                </p>
                <p class="text-[9px] font-bold text-slate-600 mt-1">Skor benar</p>
            </div>

            {{-- Jumlah Benar --}}
            <div class="rounded-2xl p-3 text-center" style="background-color: #D1FAE5;">
                <p class="font-display font-black text-xl text-[#10B981] font-mono"
                   x-text="correctCount">
                </p>
                <p class="text-[9px] font-bold text-slate-600 mt-1">Benar</p>
            </div>

            {{-- Jumlah Salah --}}
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
        examId,
        loading: true,
        examTitle: 'Ujian Talent Mapping',
        examCategory: 'Umum',
        totalScore: 0,
        correctCount: 0,
        wrongCount: 0,
        feedbackText: 'Memuat analisis...',
        topInclination: null,
        topScore: 0,
        careerPredictions: {},
        hasBreakdown: false,

        breakdown: [
            { label: 'Gambar (G)',     value: 0, color: '#E85D4E' },
            { label: 'Cerita (C)',     value: 0, color: '#5B50E5' },
            { label: 'Layout (L)',     value: 0, color: '#10B981' },
            { label: 'Warna (W)',      value: 0, color: '#FBBF24' },
            { label: 'Animasi (A)',    value: 0, color: '#06B6D4' },
            { label: 'Matematika (M)', value: 0, color: '#8B5CF6' },
            { label: 'Algoritma (A)',  value: 0, color: '#EC4899' },
        ],

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

                // 🎯 Cocokkan exam pakai hash_id (bukan raw exam_id)
                const current = allStats.find(s => String(s.hash_id) === String(this.examId))
                             || allStats.find(s => String(s.exam_id) === String(this.examId))
                             || allStats[0];

                if (current) {
                    this.examTitle    = current.exam_title || 'Ujian Talent Mapping';
                    this.examCategory = current.category || current.subcategory || 'Umum';
                    this.totalScore   = Math.round(current.mc_accuracy_pct || 0);
                    this.correctCount = current.mc_correct_count ?? 0;
                    this.wrongCount   = current.mc_wrong_count   ?? 0;
                }

                // 📊 GCLWAMA (dari data.student, global)
                const gcl = student.gclwama_breakdown || {};
                this.breakdown = [
                    { label: 'Gambar (G)',     value: Math.round(gcl['Gambar (G)']     ?? 0), color: '#E85D4E' },
                    { label: 'Cerita (C)',     value: Math.round(gcl['Cerita (C)']     ?? 0), color: '#5B50E5' },
                    { label: 'Layout (L)',     value: Math.round(gcl['Layout (L)']     ?? 0), color: '#10B981' },
                    { label: 'Warna (W)',      value: Math.round(gcl['Warna (W)']      ?? 0), color: '#FBBF24' },
                    { label: 'Animasi (A)',    value: Math.round(gcl['Animasi (A)']    ?? 0), color: '#06B6D4' },
                    { label: 'Matematika (M)', value: Math.round(gcl['Matematika (M)'] ?? 0), color: '#8B5CF6' },
                    { label: 'Algoritma (A)',  value: Math.round(gcl['Algoritma (A)']  ?? 0), color: '#EC4899' },
                ];
                this.hasBreakdown = this.breakdown.some(b => b.value > 0);

                // 🎯 Career Predictions
                this.topInclination    = student.top_inclination || null;
                this.topScore          = Math.round(student.top_score || 0);
                this.careerPredictions = student.career_predictions || {};

                // ✍️ Feedback text dinamis
                if (this.totalScore >= 70) {
                    this.feedbackText = `Kamu memiliki jiwa visual dan estetika yang baik untuk dipadukan dengan teknis. Kamu juga mampu memahami dan menghasilkan sebuah karya visual, sehingga kamu berbakat! Bidang ${this.examTitle} adalah tempat terbentuk kamu untuk menyalurkan bakat yang kamu miliki.`;
                } else if (this.totalScore >= 40) {
                    this.feedbackText = `Kamu sudah memiliki dasar yang cukup baik di bidang ${this.examTitle}. Teruslah berlatih untuk mengasah kemampuan visual dan teknis kamu, sehingga bakat tersebut dapat berkembang maksimal.`;
                } else {
                    this.feedbackText = `Bakat kamu di bidang ${this.examTitle} masih bisa terus dikembangkan. Jangan berkecil hati, teruslah belajar dan berlatih untuk menemukan gaya unik kamu sendiri!`;
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