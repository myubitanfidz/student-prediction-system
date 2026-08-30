@extends('layouts.app')
@section('title', 'Talent Mapping — Beranda')

@section('content')
<div x-data="berandaPage" class="font-sans text-slate-800 bg-[#FFFDF0] min-h-screen overflow-x-hidden">

    {{-- 1. HERO SECTION --}}
    <section id="home" class="relative pt-12 pb-16 px-4 flex flex-col items-center justify-center text-center">
        {{-- Stacked Card Paper Effect --}}
        <div class="relative max-w-2xl w-full mx-auto">
            <div class="absolute inset-0 bg-white/60 rounded-3xl transform -rotate-2 scale-102 shadow-sm border border-slate-200/50"></div>
            <div class="absolute inset-0 bg-white/80 rounded-3xl transform rotate-1 scale-101 shadow-sm border border-slate-200/60"></div>

            {{-- Main Hero Card --}}
            <div class="relative bg-white rounded-3xl p-8 sm:p-12 shadow-md border border-slate-200/80 space-y-6">
                <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-[#0984E3] tracking-tight drop-shadow-sm">
                    Kira-kira bakat ku apa, Ya?
                </h1>
                
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Setiap langkah besar dimulai dari mengenal diri sendiri! Mulai tes sekarang dan temukan potensi yang bisa membawamu meraih impian di masa depan.
                </p>

                <div>
                    <a href="#bidang" class="inline-block bg-[#FDCB6E] hover:bg-[#F39C12] text-slate-900 font-bold px-8 py-3 rounded-full text-sm sm:text-base shadow-sm hover:shadow transition transform active:scale-95">
                        Cari tau sekarang!
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. MARQUEE BANNER --}}
    <div class="w-full bg-[#FF7675] py-2.5 overflow-hidden whitespace-nowrap border-y border-[#D63031]/20 select-none">
        <div class="inline-block animate-marquee text-white text-xs sm:text-sm font-bold tracking-wider uppercase">
            <span>Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp; Find some "Talent" &nbsp;✦&nbsp;</span>
        </div>
    </div>

    {{-- 3. SECTION: PILIH BIDANG --}}
    <section id="bidang" class="max-w-4xl mx-auto px-4 py-16 text-center space-y-10">
        <div class="space-y-3">
            <h2 class="font-display font-black text-2xl sm:text-3xl text-[#2D3436] max-w-xl mx-auto leading-snug">
                Setiap orang punya cara hebatnya masing-masing. Pilih bidang yang membuatmu penasaran dan ingin terus belajar.
            </h2>
        </div>

        {{-- Highlight Sub-card Container --}}
        <div class="bg-[#FFF8E7] border-2 border-dashed border-[#FDCB6E] rounded-3xl p-6 sm:p-10 space-y-8">
            <div class="space-y-1">
                <h3 class="font-display font-extrabold text-2xl text-[#E17055]">Lihat bidang favorit kamu!</h3>
                <p class="text-xs text-slate-500">Klik salah satu kartu untuk melihat penjelasan lengkap, tahapan belajar, dan rekomendasi aplikasi!</p>
            </div>

            {{-- Kartu Pilihan: Bahasa & IT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-2xl mx-auto">
                {{-- KARTU BAHASA --}}
                <a href="{{ route('beranda.bahasa') }}" class="group relative block text-left">
                    <div class="absolute inset-0 bg-white/70 rounded-2xl border border-slate-200 transform -rotate-3 group-hover:-rotate-4 transition-transform"></div>
                    <div class="relative bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-sm group-hover:shadow-md transition space-y-4">
                        <h4 class="font-display font-black text-2xl text-[#00CEC9]">Bahasa</h4>
                        
                        <div class="w-full h-32 bg-[#F5F6FA] rounded-xl border border-slate-100 flex items-center justify-center p-3 relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#00CEC9_1px,transparent_1px)] [background-size:10px_10px]"></div>
                            <span class="text-xs font-semibold text-slate-400">Preview Modul Bahasa</span>
                        </div>

                        <div class="flex justify-between items-center text-xs font-semibold text-slate-600 pt-1">
                            <span class="bg-slate-100 px-2.5 py-1 rounded-md">Arabic</span>
                            <span class="bg-slate-100 px-2.5 py-1 rounded-md">English</span>
                        </div>
                    </div>
                </a>

                {{-- KARTU IT --}}
                <a href="{{ route('beranda.it') }}" class="group relative block text-left">
                    <div class="absolute inset-0 bg-white/70 rounded-2xl border border-slate-200 transform rotate-3 group-hover:rotate-4 transition-transform"></div>
                    <div class="relative bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-sm group-hover:shadow-md transition space-y-4">
                        <h4 class="font-display font-black text-2xl text-[#0984E3]">IT</h4>
                        
                        <div class="w-full h-32 bg-[#F5F6FA] rounded-xl border border-slate-100 flex items-center justify-center p-3 relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#0984E3_1px,transparent_1px)] [background-size:10px_10px]"></div>
                            <span class="text-xs font-semibold text-slate-400">Preview Modul IT</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-[11px] font-semibold text-slate-600 pt-1 text-center">
                            <span class="bg-slate-100 px-2 py-1 rounded-md">DKV</span>
                            <span class="bg-slate-100 px-2 py-1 rounded-md">Comic</span>
                            <span class="bg-slate-100 px-2 py-1 rounded-md">Videografi</span>
                            <span class="bg-slate-100 px-2 py-1 rounded-md">Programming</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- 4. SECTION: TESTIMONI STICKY NOTES --}}
    <section class="w-full bg-[#E17055] py-14 px-4 text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto space-y-8">
            <h3 class="font-display font-bold text-xl sm:text-2xl text-left">Ini kata mereka tentang Talent Mapping...</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                <div class="relative bg-white text-slate-800 p-6 rounded-2xl shadow-lg transform -rotate-2 space-y-4">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-5 h-5 rounded-full bg-[#0984E3] border-2 border-white shadow"></div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">"Lewat tes ini saya jadi yakin memilih peminatan coding tanpa ragu!"</p>
                    <div class="pt-2 border-t border-slate-100">
                        <p class="font-bold text-xs text-slate-900">Santri 1</p>
                        <p class="text-[10px] text-[#0984E3] font-semibold">Bidang Programming</p>
                    </div>
                </div>

                <div class="relative bg-white text-slate-800 p-6 rounded-2xl shadow-lg transform rotate-1 space-y-4">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-5 h-5 rounded-full bg-[#00CEC9] border-2 border-white shadow"></div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">"Pemetaan bakatnya tepat banget sesuai portofolio desain poster yang pernah saya buat."</p>
                    <div class="pt-2 border-t border-slate-100">
                        <p class="font-bold text-xs text-slate-900">Santri 2</p>
                        <p class="text-[10px] text-[#00CEC9] font-semibold">Bidang DKV</p>
                    </div>
                </div>

                <div class="relative bg-white text-slate-800 p-6 rounded-2xl shadow-lg transform -rotate-1 space-y-4">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-5 h-5 rounded-full bg-[#6C5CE7] border-2 border-white shadow"></div>
                    <p class="text-xs text-slate-600 leading-relaxed italic">"Soal-soal tesnya interaktif dan bikin makin semangat belajar bahasa asing."</p>
                    <div class="pt-2 border-t border-slate-100">
                        <p class="font-bold text-xs text-slate-900">Santri 3</p>
                        <p class="text-[10px] text-[#6C5CE7] font-semibold">Bidang Arabic</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. SECTION: WHY TALENT MAPPING --}}
    <section id="about" class="max-w-5xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <div class="space-y-4">
            <h3 class="font-display font-black text-3xl text-[#6C5CE7]">Why Talent Mapping?</h3>
            <p class="text-sm text-slate-600 leading-relaxed text-justify">
                Melalui Talent Mapping, kamu bisa mengenal potensi diri lebih dalam, menemukan bidang yang paling sesuai dengan kekuatanmu, serta memahami langkah yang tepat untuk mengembangkan kemampuan tersebut. Dengan mengetahui kelebihan sejak dini, kamu dapat belajar dengan lebih percaya diri, fokus pada hal yang kamu sukai, dan mempersiapkan masa depan yang lebih terarah.
            </p>
        </div>

        <div class="w-full h-64 bg-[#DFE6E9] rounded-3xl border border-slate-300 flex items-center justify-center p-6 relative overflow-hidden">
            <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#6C5CE7_1px,transparent_1px)] [background-size:12px_12px]"></div>
            <span class="text-sm font-semibold text-slate-500">Ilustrasi / Media Pendukung</span>
        </div>
    </section>

    {{-- 6. SECTION: WHAT YOU'LL GET --}}
    <section class="w-full bg-[#0984E3] py-16 px-4 text-white text-center relative">
        <div class="max-w-4xl mx-auto space-y-10">
            <h3 class="inline-block bg-[#FDCB6E] text-slate-900 font-display font-extrabold text-2xl sm:text-3xl px-8 py-2.5 rounded-full shadow">
                What You'll Get ?
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                <div class="bg-white text-slate-800 p-4 rounded-2xl font-bold text-xs shadow-sm">
                    Laporan Prediksi Potensi
                </div>
                <div class="bg-white text-slate-800 p-4 rounded-2xl font-bold text-xs shadow-sm">
                    Rekomendasi Peminatan Belajar
                </div>
                <div class="bg-white text-slate-800 p-4 rounded-2xl font-bold text-xs shadow-sm">
                    Evaluasi Portofolio Mandiri
                </div>
                <div class="bg-white text-slate-800 p-4 rounded-2xl font-bold text-xs shadow-sm">
                    Peringkat Kecakapan (A1-C2 / Pro)
                </div>
                <div class="bg-white text-slate-800 p-4 rounded-2xl font-bold text-xs shadow-sm">
                    Akses Bank Soal Lengkap
                </div>
                <div class="bg-white text-slate-800 p-4 rounded-2xl font-bold text-xs shadow-sm">
                    Sertifikat Penempatan Kelas
                </div>
            </div>
        </div>
    </section>

    {{-- 7. FOOTER --}}
    <footer class="w-full bg-[#2D3436] text-white pt-14 pb-8 px-6 lg:px-16 space-y-10">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-3 max-w-sm">
                <h4 class="font-display font-extrabold text-xl text-[#FDCB6E]">Sekolah Impian</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Pendidikan terpadu modern yang mengarahkan santri dengan bakat dan keahlian masa depan.
                </p>
            </div>

            <div class="w-full h-40 bg-slate-800/80 rounded-2xl border border-slate-700 flex items-center justify-center">
                <span class="text-xs text-slate-500">Logo / Banner Penutup</span>
            </div>
        </div>

        <div class="border-t border-slate-700/60 pt-6 text-center text-xs text-slate-500">
            &copy; 2026 Talent Mapping — Talenta Santri. All rights reserved.
        </div>
    </footer>

</div>

<style>
@keyframes marquee {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    display: inline-block;
    animation: marquee 20s linear infinite;
}
</style>
@endsection