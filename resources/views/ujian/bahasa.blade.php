@extends('layouts.app')
@section('title', '2 Bahasa Utama — Talent Mapping')

@section('content')
<div x-data="bahasaPage" class="min-h-[calc(100vh-4rem)] bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-10">

        {{-- Header Section --}}
        <div class="text-center space-y-3">
            <p class="text-sm font-medium text-slate-700">Pilih hal yang kamu minati</p>
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight">
                2 Bahasa Utama
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto leading-relaxed pt-1">
                Klik salah satu kartu untuk melihat penjelasan lengkap, tahapan belajar, dan rekomendasi aplikasi.
            </p>

            <div class="pt-3">
                <button type="button" @click="mulaiQuiz()"
                        class="inline-block bg-[#E5E7EB] hover:bg-[#D1D5DB] text-slate-900 font-extrabold text-sm sm:text-base px-8 py-3 rounded-full transition shadow-sm active:scale-95">
                    Ayo mulai quiznya sekarang!
                </button>
            </div>
        </div>

        {{-- Cards List --}}
        <div class="space-y-5">
            {{-- Kartu Bahasa Arab --}}
            <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-slate-100 shadow-sm">
                <div class="space-y-2 max-w-md">
                    <h2 class="font-display font-extrabold text-2xl text-slate-900">Bahasa Arab</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pelajari bahasa Al-Qur'an, tata bahasa qawaid, dan percakapan untuk memperdalam literatur klasik &amp; modern.
                    </p>
                </div>
                <button type="button" @click="openModal('arab')"
                        class="bg-[#8C8C8C] hover:bg-[#737373] text-white text-xs sm:text-sm font-semibold px-6 py-3 rounded-xl transition shrink-0">
                    Pelajari sekarang
                </button>
            </div>

            {{-- Kartu Bahasa Inggris --}}
            <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-slate-100 shadow-sm">
                <div class="space-y-2 max-w-md">
                    <h2 class="font-display font-extrabold text-2xl text-slate-900">Bahasa Inggris</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Kuasai bahasa internasional untuk komunikasi global, pemahaman teknologi, dan literatur sains dunia.
                    </p>
                </div>
                <button type="button" @click="openModal('inggris')"
                        class="bg-[#8C8C8C] hover:bg-[#737373] text-white text-xs sm:text-sm font-semibold px-6 py-3 rounded-xl transition shrink-0">
                    Pelajari sekarang
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW MATERI (Desktop - 23) --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div @click.outside="modalOpen = false"
             class="bg-[#F8F9FA] rounded-3xl max-w-2xl w-full p-6 sm:p-10 space-y-6 shadow-2xl relative border border-slate-200">
            
            {{-- Close Button --}}
            <button type="button" @click="modalOpen = false" class="absolute top-6 right-6 text-slate-700 hover:text-slate-950 font-display font-bold text-xl">
                ✕
            </button>

            {{-- Headline --}}
            <h3 class="font-display font-black text-2xl sm:text-3xl text-[#0984E3] leading-tight" x-html="selectedData.headline"></h3>

            {{-- Deskripsi --}}
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed" x-html="selectedData.description"></p>

            {{-- Manfaat Box --}}
            <div class="border border-slate-800 rounded-2xl p-5 space-y-2 bg-transparent">
                <h4 class="font-display font-extrabold text-sm sm:text-base text-[#E17055]">Manfaat</h4>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed" x-text="selectedData.benefits"></p>
            </div>

            {{-- Aplikasi Pendukung --}}
            <div class="space-y-3 pt-2">
                <h4 class="font-display font-extrabold text-sm sm:text-base text-[#E17055]">Aplikasi Pendukung</h4>
                <div class="flex flex-wrap gap-2.5">
                    <template x-for="app in selectedData.apps" :key="app">
                        <span class="bg-[#D9D9D9] text-slate-800 text-xs sm:text-sm font-medium px-5 py-2 rounded-full" x-text="app"></span>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bahasaPage', () => ({
        modalOpen: false,
        selectedData: {
            headline: '',
            description: '',
            benefits: '',
            apps: []
        },
        materi: {
            arab: {
                headline: 'Selamat datang di dunia, di mana <span class="bg-[#FDCB6E] text-slate-900 px-1">bahasa Arab</span> menghubungkan makna mendalam.',
                description: 'Bahasa Arab adalah bahasa wahyu dan peradaban yang kaya akan tata bahasa (Nahwu &amp; Sharaf), balaghah, dan mufradat yang indah untuk memahami literatur Islam dan diplomasi global.',
                benefits: 'Meningkatkan ketajaman logika gramatikal, pemahaman tekstual sumber kitab klasik, serta kemampuan komunikasi aktif di kancah timur tengah.',
                apps: ['Al-Maany', 'Duolingo', 'Google Translate', 'Tashreef App']
            },
            inggris: {
                headline: 'Selamat datang di dunia, di mana <span class="bg-[#FDCB6E] text-slate-900 px-1">bahasa Inggris</span> membuka jendela dunia.',
                description: 'Bahasa Inggris adalah kunci utama akses informasi global, sains, dan teknologi masa kini. Kuasai grammar, vocabulary, dan speaking untuk bersaing di era digital.',
                benefits: 'Membuka wawasan internasional, mempermudah adaptasi dengan software dan dokumentasi teknologi, serta meningkatkan daya saing karir masa depan.',
                apps: ['Grammarly', 'Duolingo', 'Cambridge Dictionary', 'ELSA Speak']
            }
        },
        openModal(type) {
            this.selectedData = this.materi[type];
            this.modalOpen = true;
        },
        async mulaiQuiz() {
            try {
                const res = await fetch('/api/exams', {
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('ts_token')}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const bahasaExams = json?.data?.Bahasa || [];
                if (bahasaExams.length > 0) {
                    window.location.href = `/ujian/${bahasaExams[0].id}`;
                } else {
                    alert('Paket ujian Bahasa belum tersedia.');
                }
            } catch {
                window.location.href = '/beranda';
            }
        }
    }));
});
</script>
@endsection