@extends('layouts.app')
@section('title', 'Hasil Pengerjaan — Talent Mapping')

@section('content')
<div x-data="resultPage" class="min-h-[calc(100vh-4rem)] bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-10" x-show="!loading" x-cloak>

        {{-- Header Status --}}
        <div class="text-center space-y-2">
            <div class="w-6 h-6 bg-slate-950 rounded-full mx-auto mb-3"></div>
            <h1 class="font-display font-black text-3xl sm:text-4xl text-slate-900">
                Pengerjaan selesai!
            </h1>
            <p class="text-sm text-slate-600">
                kamu berhasil menuntaskan tes Talent Mapping
            </p>
        </div>

        {{-- Main Result Card (Desktop - 13) --}}
        <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm space-y-8">
            
            {{-- Top Result Banner --}}
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b border-slate-200/60 pb-8">
                <div class="space-y-1">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Memiliki kekuatan bakat dibidang:</p>
                    <h2 class="font-display font-black text-2xl sm:text-4xl text-slate-900" x-text="topCategory">
                        Desain Komunikasi Visual (DKV)
                    </h2>
                </div>
                <div class="text-left sm:text-right shrink-0">
                    <p class="text-xs uppercase tracking-wider text-slate-500 font-bold">SKOR KESELURUHAN</p>
                    <p class="font-display font-black text-4xl sm:text-6xl text-slate-900 mt-1" x-text="`${overallScore}%`">
                        100%
                    </p>
                </div>
            </div>

            {{-- Grid Preview & Rincian Skor Bar --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                {{-- Placeholder Ilustrasi Kotak Transparan --}}
                <div class="w-full h-56 sm:h-64 bg-white rounded-2xl border border-slate-200 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#8C8C8C_1.5px,transparent_1.5px)] [background-size:12px_12px]"></div>
                </div>

                {{-- Bar Rincian Skor --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 space-y-4">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Rincian Skor</h3>
                        <p class="text-[11px] text-slate-500">Detail perolehan skor perkategori</p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <template x-for="item in subScores" :key="item.label">
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs font-semibold text-slate-800">
                                    <span x-text="item.label"></span>
                                </div>
                                <div class="w-full h-3 rounded-full bg-[#E5E7EB] overflow-hidden">
                                    <div class="h-full bg-[#8C8C8C] transition-all duration-500"
                                         :style="`width: ${item.score}%`"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Narrative & Stats Card --}}
        <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm space-y-8">
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed max-w-3xl" x-text="narrativeText">
                Kamu memiliki jiwa visual dan estetika yang baik untuk dapat mengolah desain kedepannya serta kemampuan penataan desain kamu sangat baik! Bidang DKV adalah tempat terbaik untuk kamu menyalurkan bakat yang kamu miliki.
            </p>

            {{-- 3 Box Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[#D9D9D9] p-6 rounded-2xl text-center space-y-1">
                    <p class="font-display font-black text-2xl sm:text-3xl text-slate-900" x-text="`${accuracy}%`">90%</p>
                    <p class="text-xs font-semibold text-slate-700">Skor benar</p>
                </div>
                <div class="bg-[#D9D9D9] p-6 rounded-2xl text-center space-y-1">
                    <p class="font-display font-black text-2xl sm:text-3xl text-slate-900" x-text="correctCount">45</p>
                    <p class="text-xs font-semibold text-slate-700">Benar</p>
                </div>
                <div class="bg-[#D9D9D9] p-6 rounded-2xl text-center space-y-1">
                    <p class="font-display font-black text-2xl sm:text-3xl text-slate-900" x-text="wrongCount">5</p>
                    <p class="text-xs font-semibold text-slate-700">Salah</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="text-center space-y-4 pt-2">
            <div>
                <a href="{{ route('portofolio.index') }}"
                   class="inline-block bg-[#D9D9D9] hover:bg-[#C8C8C8] text-slate-900 font-extrabold text-sm sm:text-base px-10 py-3.5 rounded-2xl transition shadow-xs">
                    Upload Portofolio terbaik kamu!
                </a>
            </div>
            <div>
                <a href="{{ route('beranda') }}" class="text-xs sm:text-sm text-slate-800 hover:underline font-semibold inline-block">
                    Mau melakukan Quiz ulang?
                </a>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('resultPage', () => ({
        loading: true,
        topCategory: 'Desain Komunikasi Visual (DKV)',
        overallScore: 100,
        accuracy: 90,
        correctCount: 45,
        wrongCount: 5,
        narrativeText: 'Kamu memiliki jiwa visual dan estetika yang baik untuk dapat mengolah desain kedepannya serta kemampuan penataan desain kamu sangat baik! Bidang DKV adalah tempat terbaik untuk kamu menyalurkan bakat yang kamu miliki.',
        subScores: [
            { label: 'Gambar', score: 92 },
            { label: 'Cerita', score: 88 },
            { label: 'Layout', score: 85 },
            { label: 'Matematika', score: 65 }
        ],

        async init() {
            try {
                const res = await fetch('/api/dashboard', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('ts_token')}`,
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                const stats = json?.data?.exam_stats || [];

                if (stats.length > 0) {
                    const top = [...stats].sort((a, b) => (b.mc_accuracy_pct || 0) - (a.mc_accuracy_pct || 0))[0];
                    this.topCategory = top.exam_title || top.subcategory || 'IT';
                    this.overallScore = Math.round(stats.reduce((a, b) => a + (b.mc_accuracy_pct || 0), 0) / stats.length);
                    this.accuracy = top.mc_accuracy_pct || 85;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection