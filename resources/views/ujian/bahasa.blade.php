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
                {{-- Membuka modal kecil pilihan subkategori --}}
                <button type="button" @click="quizChoiceModalOpen = true"
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
            
            <button type="button" @click="modalOpen = false" class="absolute top-6 right-6 text-slate-700 hover:text-slate-950 font-display font-bold text-xl">
                ✕
            </button>

            <h3 class="font-display font-black text-2xl sm:text-3xl text-[#0984E3] leading-tight" x-html="selectedData.headline"></h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed" x-html="selectedData.description"></p>

            <div class="border border-slate-800 rounded-2xl p-5 space-y-2 bg-transparent">
                <h4 class="font-display font-extrabold text-sm sm:text-base text-[#E17055]">Manfaat</h4>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed" x-text="selectedData.benefits"></p>
            </div>

            <div class="space-y-3 pt-2">
                <h4 class="font-display font-extrabold text-sm sm:text-base text-[#E17055]">Aplikasi Pendukung</h4>
                <div class="flex flex-wrap gap-2.5">
                    <template x-for="app in selectedData.apps" :key="app">
                        <span class="bg-[#D9D9D9] text-slate-800 text-xs sm:text-sm font-medium px-5 py-2 rounded-full" x-text="app"></span>
                    </template>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="button" @click="mulaiQuiz(selectedKey)"
                        class="bg-[#0984E3] hover:bg-[#0773c5] text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl transition shadow active:scale-95">
                    Mulai Ujian Materi Ini →
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL PILIHAN QUIZ (POP-UP KECIL ARAB / INGGRIS) --}}
    <div x-show="quizChoiceModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div @click.outside="quizChoiceModalOpen = false"
             class="bg-white rounded-3xl max-w-sm w-full p-6 sm:p-8 space-y-6 shadow-2xl relative border border-slate-200 text-center">
            
            <button type="button" @click="quizChoiceModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 font-bold text-lg">
                ✕
            </button>

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
    Alpine.data('bahasaPage', () => ({
        modalOpen: false,
        quizChoiceModalOpen: false,
        selectedKey: '',
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
            this.selectedKey = type;
            this.selectedData = this.materi[type];
            this.modalOpen = true;
        },
        async mulaiQuiz(subcategory = null) {
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
                const rawData = json?.data ?? json ?? {};

                // Normalisasi daftar ujian
                let allExams = [];
                if (Array.isArray(rawData)) {
                    allExams = rawData;
                } else {
                    Object.keys(rawData).forEach(cat => {
                        if (Array.isArray(rawData[cat])) {
                            allExams.push(...rawData[cat]);
                        }
                    });
                }

                // Filter kategori Bahasa
                let targetExams = allExams.filter(e => 
                    (e.category || '').trim().toLowerCase() === 'bahasa'
                );

                // Filter subkategori jika dikirim (arab / inggris)
                if (subcategory) {
                    const filteredBySub = targetExams.filter(e => 
                        (e.subcategory || e.title || '').toLowerCase().includes(subcategory.toLowerCase())
                    );
                    if (filteredBySub.length > 0) {
                        targetExams = filteredBySub;
                    }
                }

                if (targetExams.length > 0) {
                    window.location.href = `/ujian/${targetExams[0].id}`;
                } else {
                    alert(`Paket ujian Bahasa ${subcategory ? '(' + subcategory.toUpperCase() + ')' : ''} belum dibuka atau belum memiliki soal.`);
                }
            } catch (err) {
                console.error(err);
                alert('Gagal memuat paket ujian. Silakan periksa jaringan server.');
            }
        }
    }));
});
</script>
@endsection