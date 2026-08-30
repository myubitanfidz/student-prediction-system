@extends('layouts.app')
@section('title', '4 Bidang Utama IT — Talent Mapping')

@section('content')
<div x-data="itPage" class="min-h-[calc(100vh-4rem)] bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-10">

        {{-- Header Section --}}
        <div class="text-center space-y-3">
            <p class="text-sm font-medium text-slate-700">Pilih hal yang kamu minati</p>
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight">
                4 Bidang Utama
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

        {{-- Cards List (4 Bidang Utama) --}}
        <div class="space-y-5">
            {{-- 1. Programming --}}
            <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-slate-100 shadow-sm">
                <div class="space-y-2 max-w-md">
                    <h2 class="font-display font-extrabold text-2xl text-slate-900">Programming</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Bangun produk nyata dari logika dan baris kode yang kamu ciptakan.
                    </p>
                </div>
                <button type="button" @click="openModal('programming')"
                        class="bg-[#8C8C8C] hover:bg-[#737373] text-white text-xs sm:text-sm font-semibold px-6 py-3 rounded-xl transition shrink-0">
                    Pelajari sekarang
                </button>
            </div>

            {{-- 2. Desain Komunikasi Visual (DKV) --}}
            <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-slate-100 shadow-sm">
                <div class="space-y-2 max-w-md">
                    <h2 class="font-display font-extrabold text-2xl text-slate-900">Desain Komunikasi Visual (DKV)</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Ubah ide jadi identitas visual yang kuat, elegan, dan berkesan.
                    </p>
                </div>
                <button type="button" @click="openModal('dkv')"
                        class="bg-[#8C8C8C] hover:bg-[#737373] text-white text-xs sm:text-sm font-semibold px-6 py-3 rounded-xl transition shrink-0">
                    Pelajari sekarang
                </button>
            </div>

            {{-- 3. Komik --}}
            <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-slate-100 shadow-sm">
                <div class="space-y-2 max-w-md">
                    <h2 class="font-display font-extrabold text-2xl text-slate-900">Komik</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Ceritakan duniamu lewat panel, warna, dan karakter yang penuh jiwa.
                    </p>
                </div>
                <button type="button" @click="openModal('komik')"
                        class="bg-[#8C8C8C] hover:bg-[#737373] text-white text-xs sm:text-sm font-semibold px-6 py-3 rounded-xl transition shrink-0">
                    Pelajari sekarang
                </button>
            </div>

            {{-- 4. Videografi --}}
            <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-slate-100 shadow-sm">
                <div class="space-y-2 max-w-md">
                    <h2 class="font-display font-extrabold text-2xl text-slate-900">Videografi</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Tangkap momen, rangkai cerita, dan ciptakan karya visual bergerak.
                    </p>
                </div>
                <button type="button" @click="openModal('videografi')"
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
    Alpine.data('itPage', () => ({
        modalOpen: false,
        selectedData: {
            headline: '',
            description: '',
            benefits: '',
            apps: []
        },
        materi: {
            programming: {
                headline: 'Selamat datang di dunia, di mana <span class="bg-[#FDCB6E] text-slate-900 px-1">logika baris kode</span> memecahkan masalah.',
                description: 'Programming adalah bidang logika komputasi untuk membangun aplikasi web, mobile, dan sistem pintar yang memudahkan kehidupan banyak orang.',
                benefits: 'Mengasah kemampuan berpikir kritis sistematis (computational thinking), pemecahan masalah algoritma, serta membangun solusi software siap pakai.',
                apps: ['VS Code', 'Git / GitHub', 'Postman', 'Laragon / Docker']
            },
            dkv: {
                headline: 'Selamat datang di dunia, di mana <span class="bg-[#FDCB6E] text-slate-900 px-1">visual mampu bercerita.</span>',
                description: 'DKV atau Desain Komunikasi Visual adalah bidang yang menggunakan <span class="text-[#E17055] font-semibold">elemen visual untuk menyampaikan ide, informasi, atau pesan.</span> Kamu akan belajar bagaimana mengolah warna, bentuk, gambar, tipografi, dan layout menjadi sebuah karya yang menarik sekaligus memiliki tujuan.',
                benefits: 'Belajar desain membantu mengembangkan kreativitas, kemampuan visual, ketelitian, dan kemampuan menyampaikan pesan melalui gambar. Kamu juga bisa belajar membuat sesuatu yang tidak hanya menarik, tetapi juga memiliki tujuan.',
                apps: ['Canva', 'Adobe Illustrator', 'Figma', 'Affinity']
            },
            komik: {
                headline: 'Selamat datang di dunia, di mana <span class="bg-[#FDCB6E] text-slate-900 px-1">cerita &amp; ilustrasi</span> menyatu.',
                description: 'Komik memadukan kekuatan cerita (storytelling) dengan visual gambar berurutan untuk menghadirkan ekspresi karakter, drama, dan pesan moral yang menyenangkan dibaca.',
                benefits: 'Melatih kemampuan merangkai storyboard, anatomi karakter visual, komposisi warna panel, dan eksplorasi narasi kreatif.',
                apps: ['Clip Studio Paint', 'Procreate', 'Photoshop', 'Ibis Paint']
            },
            videografi: {
                headline: 'Selamat datang di dunia, di mana <span class="bg-[#FDCB6E] text-slate-900 px-1">sinematografi</span> mengabadikan emosi.',
                description: 'Videografi mengombinasikan framing kamera, pencahayaan, tata suara, dan teknik editing video untuk menghasilkan karya audio-visual yang menginspirasi.',
                benefits: 'Menguasai keterampilan produksi video, editing sinematik, visual storytelling, serta manajemen konten multimedia modern.',
                apps: ['Premiere Pro', 'DaVinci Resolve', 'CapCut', 'After Effects']
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
                const itExams = json?.data?.IT || [];
                if (itExams.length > 0) {
                    window.location.href = `/ujian/${itExams[0].id}`;
                } else {
                    alert('Paket ujian IT belum tersedia.');
                }
            } catch {
                window.location.href = '/beranda';
            }
        }
    }));
});
</script>
@endsection