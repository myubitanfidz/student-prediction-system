@extends('layouts.app')

@php
    $bidangActive = $bidang ?? request()->query('bidang', 'bahasa');
    $isIt = strtolower($bidangActive) === 'it';
    // Popup materi sekarang dikontrol lewat query string ?materi=... (GET),
    // bukan lewat state Alpine lagi. Lihat @switch di bagian modal di bawah.
    $activeMateri = request()->query('materi');
@endphp

@section('title', $isIt ? 'Eksplorasi Bidang IT & Kreatif — Talent Mapping' : '2 Bahasa Utama — Talent Mapping')

@section('content')
<div x-data="explorePage('{{ $bidangActive }}')" 
     class="min-h-screen relative overflow-hidden flex flex-col transition-colors duration-500"
     :class="currentBidang === 'it' ? 'bg-[#f0f3f8]' : 'bg-[#fef7d9]'">
    
    {{-- ============ HEADER SECTION ============ --}}
    <div class="text-center pt-10 pb-6 px-4 relative z-10">
        <div class="max-w-4xl mx-auto flex items-center justify-between mb-4">
            <a href="{{ route('beranda') }}" class="inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-1.5 rounded-full border shadow-xs transition bg-white/80 backdrop-blur-xs"
               :class="currentBidang === 'it' ? 'text-[#0984e3] border-[#0984e3]/20 hover:text-[#0773c5]' : 'text-[#E17055] border-[#E17055]/20 hover:text-[#c4573e]'">
                ← Kembali ke Beranda
            </a>

            <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-black/5"
                  :class="currentBidang === 'it' ? 'text-[#0984e3]' : 'text-[#E17055]'"
                  x-text="currentBidang === 'it' ? 'Bidang IT & Kreatif' : 'Bidang Bahasa'">
            </span>
        </div>

        <p class="text-sm font-medium text-slate-700">Pilih hal yang kamu minati</p>
        
        <h1 class="font-display font-extrabold text-4xl sm:text-5xl tracking-tight mt-2 transition-colors duration-300"
            :class="currentBidang === 'it' ? 'text-[#2d3436]' : 'text-[#E17055]'"
            x-text="currentBidang === 'it' ? '4 Bidang Utama' : '2 Bidang Bahasa'">
        </h1>
        
        <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto leading-relaxed pt-3">
            Klik salah satu kartu untuk melihat penjelasan lengkap, tahapan belajar, dan rekomendasi aplikasi.
        </p>

        <div class="pt-5">
            {{-- Tombol Utama --}}
            <button type="button" @click="handleMainQuizBtn()"
                    class="inline-block text-white font-extrabold text-sm sm:text-base px-8 py-3 rounded-full transition shadow-sm active:scale-95"
                    :class="currentBidang === 'it' ? 'bg-[#0984e3] hover:bg-[#0773c5]' : 'bg-[#38ada9] hover:bg-[#2e8c89]'">
                Ayo mulai quiznya sekarang!
            </button>
        </div>
    </div>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="flex-1 px-4 pb-20 relative -mt-4">
        <img src="{{ asset('images/landing/Vector 10.svg') }}" 
             class="absolute top-0 left-0 w-full h-auto pointer-events-none z-0" alt="">

             <div class="max-w-2xl mx-auto space-y-4 relative z-10 pt-48"> 
    
    {{-- ====== KARTU-KARTU BAHASA ====== --}}
    <template x-if="currentBidang === 'bahasa'">
        <div class="space-y-4">
            {{-- Bahasa Arab --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-xs border border-slate-100 flex flex-col transition hover:shadow-md">
                <div class="w-full h-28 sm:h-36 bg-slate-200 relative overflow-hidden">
                    <img src="{{ asset('images/landing/BahasaArab.jpg') }}" 
                         alt="Bahasa Arab Banner" 
                         class="w-full h-full object-cover">
                </div>
                
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1 max-w-md">
                        <h2 class="font-display font-extrabold text-lg sm:text-xl text-slate-900">Bahasa Arab</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pelajari bahasa Al-Qur'an, tata bahasa qawaid, dan percakapan untuk memperdalam literatur klasik &amp; modern.
                        </p>
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['materi' => 'arab']) }}"
                       class="bg-[#E85D4E] hover:bg-[#d44c3d] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shrink-0 text-center shadow-xs active:scale-95">
                        Pelajari sekarang
                    </a>
                </div>
            </div>

            {{-- Bahasa Inggris --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-xs border border-slate-100 flex flex-col transition hover:shadow-md">
                <div class="w-full h-28 sm:h-36 bg-slate-200 relative overflow-hidden">
                    <img src="{{ asset('images/landing/BahasaInggris.jpg') }}" 
                         alt="Bahasa Inggris Banner" 
                         class="w-full h-full object-cover">
                </div>
                
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1 max-w-md">
                        <h2 class="font-display font-extrabold text-lg sm:text-xl text-slate-900">Bahasa Inggris</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Kuasai bahasa internasional untuk komunikasi global, pemahaman teknologi, dan literatur sains dunia.
                        </p>
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['materi' => 'inggris']) }}"
                       class="bg-[#E85D4E] hover:bg-[#d44c3d] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shrink-0 text-center shadow-xs active:scale-95">
                        Pelajari sekarang
                    </a>
                </div>
            </div>
        </div>
    </template>

    {{-- ====== KARTU-KARTU IT ====== --}}
    <template x-if="currentBidang === 'it'">
        <div class="space-y-4">
            {{-- Programming --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-xs border border-slate-100 flex flex-col transition hover:shadow-md">
                <div class="w-full h-28 sm:h-36 bg-slate-200 relative overflow-hidden">
                    <img src="{{ asset('images/landing/programming.jpg') }}" 
                         alt="Programming Banner" 
                         class="w-full h-full object-cover">
                </div>

                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1 max-w-md">
                        <h2 class="font-display font-extrabold text-lg sm:text-xl text-slate-900">Programming</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Bangun produk nyata dari logika dan baris kode yang kamu ciptakan.
                        </p>
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['materi' => 'programming']) }}"
                       class="bg-[#E85D4E] hover:bg-[#d44c3d] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shrink-0 text-center shadow-xs active:scale-95">
                        Pelajari sekarang
                    </a>
                </div>
            </div>

            {{-- DKV / Desain Grafis --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-xs border border-slate-100 flex flex-col transition hover:shadow-md">
                <div class="w-full h-28 sm:h-36 bg-slate-200 relative overflow-hidden">
                    <img src="{{ asset('images/landing/DKV.jpg') }}" 
                         alt="DKV Banner" 
                         class="w-full h-full object-cover">
                </div>

                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1 max-w-md">
                        <h2 class="font-display font-extrabold text-lg sm:text-xl text-slate-900">Desain Komunikasi Visual (DKV)</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Tangkap momen, rangkai cerita, dan ciptakan karya visual bergerak.
                        </p>
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['materi' => 'dkv']) }}"
                       class="bg-[#E85D4E] hover:bg-[#d44c3d] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shrink-0 text-center shadow-xs active:scale-95">
                        Pelajari sekarang
                    </a>
                </div>
            </div>

            {{-- Komik --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-xs border border-slate-100 flex flex-col transition hover:shadow-md">
                <div class="w-full h-28 sm:h-36 bg-slate-200 relative overflow-hidden">
                    <img src="{{ asset('images/landing/Komik.jpg') }}" 
                         alt="Komik Banner" 
                         class="w-full h-full object-cover">
                </div>

                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1 max-w-md">
                        <h2 class="font-display font-extrabold text-lg sm:text-xl text-slate-900">Komik</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Ceritakan duniamu lewat panel visual, dan karakter yang penuh jiwa.
                        </p>
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['materi' => 'komik']) }}"
                       class="bg-[#E85D4E] hover:bg-[#d44c3d] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shrink-0 text-center shadow-xs active:scale-95">
                        Pelajari sekarang
                    </a>
                </div>
            </div>

            {{-- Videografi & Editing --}}
            <div class="bg-white rounded-2xl overflow-hidden shadow-xs border border-slate-100 flex flex-col transition hover:shadow-md">
                <div class="w-full h-28 sm:h-36 bg-slate-200 relative overflow-hidden">
                    <img src="{{ asset('images/landing/videography.jpg') }}" 
                         alt="Videografi Banner" 
                         class="w-full h-full object-cover">
                </div>

                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1 max-w-md">
                        <h2 class="font-display font-extrabold text-lg sm:text-xl text-slate-900">Videografi</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Tangkap momen, rangkai cerita, dan ciptakan karya visual bergerak.
                        </p>
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['materi' => 'videografi']) }}"
                       class="bg-[#E85D4E] hover:bg-[#d44c3d] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shrink-0 text-center shadow-xs active:scale-95">
                        Pelajari sekarang
                    </a>
                </div>
            </div>
        </div>
    </template>

</div>
    {{-- ============ MODAL PREVIEW MATERI ============ --}}
    @if ($activeMateri)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            @include('student.ujian.materi')
        </div>
    @endif

    {{-- ============ MODAL PILIHAN QUIZ (HANYA UNTUK BAHASA) ============ --}}
    <div x-show="quizChoiceModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div @click.outside="quizChoiceModalOpen = false"
             class="bg-white rounded-3xl max-w-sm w-full p-6 sm:p-8 space-y-6 shadow-2xl relative border border-slate-200 text-center">
            <button type="button" @click="quizChoiceModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
            <div class="space-y-2">
                <h3 class="font-display font-bold text-xl text-slate-900">Pilih Bahasa Ujian</h3>
                <p class="text-xs text-slate-500">Pilih bidang bahasa yang ingin kamu kerjakan sekarang:</p>
            </div>

            <div class="grid grid-cols-1 gap-3 pt-2">
                <button type="button" @click="mulaiQuiz('arab')"
                        class="w-full bg-[#00CEC9] hover:bg-[#00b5b0] text-white font-extrabold py-3.5 px-5 rounded-2xl transition shadow-sm active:scale-95 flex items-center justify-center gap-2">
                    <span>📖</span>
                    <span>Bahasa Arab</span>
                </button>
                <button type="button" @click="mulaiQuiz('inggris')"
                        class="w-full bg-[#0984E3] hover:bg-[#0773c5] text-white font-extrabold py-3.5 px-5 rounded-2xl transition shadow-sm active:scale-95 flex items-center justify-center gap-2">
                    <span>🌍</span>
                    <span>Bahasa Inggris</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('explorePage', (initialBidang) => ({
        currentBidang: (initialBidang || 'bahasa').toLowerCase(),
        quizChoiceModalOpen: false,

        handleMainQuizBtn() {
            if (this.currentBidang === 'it') {
                this.mulaiQuiz('it');
            } else {
                this.quizChoiceModalOpen = true;
            }
        },

        async mulaiQuiz(targetKey = null) {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            try {
                const res = await fetch('/api/exams', {
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Accept': 'application/json' 
                    }
                });

                if (res.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                const json = await res.json();
                const rawData = json?.data ?? {};
                let examPool = [];

                if (Array.isArray(rawData)) {
                    examPool = rawData;
                } else {
                    Object.values(rawData).forEach(list => {
                        if (Array.isArray(list)) examPool.push(...list);
                    });
                }

                const isExamActive = (e) => Boolean(e.is_active === true || e.is_active === 1 || e.is_active === '1');
                let selectedExam = null;

                if (this.currentBidang === 'it') {
                    selectedExam = examPool.find(e => e.home_slot === 'it_gclwama' && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'it' && isExamActive(e))
                                || examPool.find(e => (e.subcategory || '').toLowerCase().includes('gclwama') && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'it');
                } else {
                    const subLower = (targetKey || '').toLowerCase();
                    const targetSlot = subLower.includes('arab') ? 'bahasa_arab' : 'bahasa_inggris';
                    
                    selectedExam = examPool.find(e => e.home_slot === targetSlot && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'bahasa' && (e.subcategory || e.title || '').toLowerCase().includes(subLower) && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'bahasa' && isExamActive(e));
                }

                if (selectedExam && selectedExam.id) {
                    window.location.href = `/ujian/${selectedExam.id}`;
                } else {
                    alert(`Paket ujian untuk ${this.currentBidang.toUpperCase()} belum diaktifkan oleh admin.`);
                }
            } catch (err) {
                console.error('Fetch exams error:', err);
                alert('Gagal menghubungi server untuk memuat ujian.');
            }
        }
    }));
});
</script>
@endsection