@extends('layouts.app')
@section('title', 'Talent Mapping — Kira-kira bakat ku apa, ya?')

@section('content')

{{-- ============ ANIMATION STYLES & CAROUSEL STYLES ============ --}}
{{-- ============ ANIMATION STYLES ============ --}}
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    
    /* Hover effect for cards */
    .card-hover:hover {
        transform: translateY(-5px) scale(1.02);
        transition: transform 0.3s ease;
    }

    /* Hide scrollbar for the testimonial slider */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Animation for the hero button overlay */
    @keyframes pulseBtn {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7); }
        50% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); }
    }
    .animate-btn-pulse {
        animation: pulseBtn 1.5s ease-in-out infinite;
    }

    /* Combined flight animation for the planes */
    @keyframes floatY {
        0%, 100% { transform: translateY(0) rotate(6deg); }
        50% { transform: translateY(-12px) rotate(8deg); }
    }
    .animate-float-plane {
        animation: floatY 3s ease-in-out infinite;
    }
</style>

{{-- ============ NAVBAR (DELETED ENTIRELY) ============ --}}
{{-- <header> ... </header> --}}

{{-- ============ HERO ============ --}}
<section class="relative w-full overflow-hidden animate-fade-up">
    <div class="relative w-full aspect-[1440/899]">
        <img src="{{ asset('images/landing/hero-plaid-bg.svg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
        <img src="{{ asset('images/landing/hero-envelope.svg') }}" alt="" class="absolute inset-0 w-full h-full object-contain pointer-events-none">
        
        {{-- Content Wrapper (Moved lower to cover the white gap) --}}
        <div class="absolute left-1/2 top-[26%] w-[62%] -translate-x-1/2">
            
            {{-- The Full Hero Text (Restored!) --}}
            <img src="{{ asset('images/landing/hero-content.svg') }}" 
                 alt="Kira-kira bakat ku apa, ya? Setiap langkah besar dimulai dari mengenal diri sendiri."
                 class="w-full h-auto pointer-events-none">

            {{-- The Clickable Yellow Button Overlay --}}
            <a href="#bidang" 
               class="absolute left-1/2 bottom-[10%] w-[40%] h-[12%] -translate-x-1/2 -rotate-180" 
               aria-label="Cari tahu sekarang!">
            </a>

        </div>

       
             {{-- Combined Plane Container --}}
             <div class="absolute top-[9%] right-[13%] w-[8%] min-w-[90px] max-w-[150px] rotate-6 animate-float-plane">
            
            {{-- The dashed line: Tucked directly into the back of the plane (Tail) --}}
            <img src="{{ asset('images/landing/plane-trail-dashed.svg') }}" alt="" 
                 class="absolute -right-20 top-[65%] w-28 opacity-80 -z-20">

            {{-- Navy plane (Middle layer) --}}
            <img src="{{ asset('images/landing/plane-navy.svg') }}" alt="" 
                 class="absolute left-[20%] top-[20%] w-[70%] -z-10">

            {{-- Yellow plane (Front layer) --}}
            <img src="{{ asset('images/landing/plane-yellow.svg') }}" alt="" 
                 class="relative w-full drop-shadow-md z-10">
        </div>
    </div>
</section>
{{-- ============ MARQUEE ============ --}}
<div class="bg-[#f26d3d] py-4 overflow-hidden">
    {{-- Note: Ideally, repeat this img several times horizontally to ensure it spans the full width, or use CSS animation --}}
    <div class="marquee-track">
        <img src="{{ asset('images/landing/marquee-tile.svg') }}" alt="" aria-hidden="true" class="h-auto w-[1440px] shrink-0">
    </div>
</div>

{{-- ============ QUOTE ============ --}}
<section class="bg-[#fef7d9] py-16 md:py-20 px-4 animate-fade-up delay-1">
    <div class="max-w-4xl mx-auto">
        <img src="{{ asset('images/landing/quote-text.svg') }}" alt="Setiap orang punya cara hebatnya masing-masing..." class="w-full h-auto">
    </div>
</section>

{{-- ============ LIHAT BIDANG FAVORIT KAMU ============ --}}
<section id="bidang" class="relative animate-fade-up delay-2">
    <img src="{{ asset('images/landing/bidang-scallop-bg.svg') }}" alt="" class="absolute inset-0 w-full h-full object-cover -z-10">
    <div class="max-w-6xl mx-auto text-center px-4 py-20 md:py-24">
        <img src="{{ asset('images/landing/title-lihat-bidang.svg') }}" alt="Lihat bidang favorit kamu!" class="mx-auto h-auto w-full max-w-xl mb-4">
        <p class="text-white/90 mb-14 max-w-xl mx-auto">Klik salah satu kartu untuk melihat penjelasan lengkap, tahapan belajar, dan rekomendasi aplikasi.</p>

        <div class="flex flex-col md:flex-row justify-center items-center gap-16 md:gap-20">
            {{-- KARTU BAHASA (Menuju /beranda/bahasa) --}}
            <a href="{{ route('beranda.bahasa') }}" class="w-full max-w-xs shrink-0 card-hover">
                <img src="{{ asset('images/landing/card-bahasa.svg') }}" alt="Bahasa — Arabic, English" class="w-full h-auto">
            </a>

            {{-- KARTU IT (Menuju /beranda/it) --}}
            <a href="{{ route('beranda.it') }}" class="relative w-full max-w-xs shrink-0 card-hover">
                <img src="{{ asset('images/landing/card-it-back.svg') }}" alt="" class="absolute -right-6 top-4 w-full h-auto -z-10 opacity-95">
                <img src="{{ asset('images/landing/card-it-front.svg') }}" alt="IT — DKV, Videografi, Comic, Programming" class="relative w-full h-auto">
            </a>
        </div>
    </div>
</section>

{{-- ============ TESTIMONIALS (SLIDING CAROUSEL) ============ --}}
<section class="relative bg-[#f26d3d] py-20 px-4 overflow-hidden animate-fade-up delay-3">
    <div class="absolute top-6 right-10 hidden sm:flex items-center">
        <img src="{{ asset('images/landing/blob-white.svg') }}" alt="" class="w-14 h-14 -mr-4">
        <img src="{{ asset('images/landing/blob-white.svg') }}" alt="" class="w-14 h-14 -mr-4">
        <img src="{{ asset('images/landing/blob-white.svg') }}" alt="" class="w-14 h-14">
    </div>

    <div class="max-w-6xl mx-auto">
        <h2 class="text-white font-medium mb-10 ml-2">Ini kata mereka tentang Talent Mapping....</h2>

        {{-- Sliding Container: Add overflow-x-auto and no-scrollbar for drag/swipe --}}
        <div class="overflow-x-auto no-scrollbar snap-x snap-mandatory">
            <div class="flex flex-row gap-10 px-4 py-4 w-max mx-auto">
                
                {{-- Person 1 --}}
                <div class="w-64 h-64 shrink-0 snap-center flex items-center justify-center card-hover">
                    <img src="{{ asset('images/landing/testimonial-programming.svg') }}" alt="Person 1 — Bidang Programming" class="w-full h-full object-contain">
                </div>

                {{-- Person 2 --}}
                <div class="w-64 h-64 shrink-0 snap-center flex items-center justify-center card-hover">
                    <img src="{{ asset('images/landing/testimonial-dkv.svg') }}" alt="Person 2 — Bidang DKV" class="w-full h-full object-contain">
                </div>

                {{-- Person 3 (Smaller size, reusing same images) --}}
                <div class="w-48 h-64 shrink-0 snap-center flex items-center justify-center card-hover">
                    <img src="{{ asset('images/landing/testimonial-arabic.svg') }}" alt="Person 3 — Bidang Arabic" class="w-full h-full object-contain">
                </div>
                
                {{-- Person 4 (Reusing Person 1 to show sliding functionality) --}}
                <div class="w-64 h-64 shrink-0 snap-center flex items-center justify-center card-hover">
                    <img src="{{ asset('images/landing/testimonial-programming.svg') }}" alt="Person 4 — Bidang Programming" class="w-full h-full object-contain">
                </div>

            </div>
        </div>
        <p class="text-white/70 text-center mt-4 text-sm md:hidden">Geser ke samping untuk melihat lebih banyak</p>
    </div>
</section>

{{-- ============ WHY TALENT MAPPING (HUGE SWIRL) ============ --}}
<section class="relative bg-[#fdfaf0] py-20 px-8 lg:px-24 overflow-hidden">
    <img src="{{ asset('images/landing/accent-square-large.svg') }}" alt="" class="absolute top-0 right-0 w-56 h-auto -z-0 pointer-events-none">
    <img src="{{ asset('images/landing/accent-square-small.svg') }}" alt="" class="absolute bottom-0 left-0 w-32 h-auto -z-0 pointer-events-none">

    {{-- THE MASSIVE SCRIBBLE: absolute positioning to cover the whole left side --}}
    <img src="{{ asset('images/landing/swirl-green.svg') }}" alt="" class="absolute -top-20 -left-20 w-[700px] max-w-none -z-10 opacity-90 pointer-events-none">

    <div class="relative flex flex-col md:flex-row items-center justify-between gap-12">
        <div class="md:w-1/2 z-10"> {{-- Added z-10 so text is above the swirl --}}
            <h2 class="text-3xl font-bold text-[#4c4586] mb-6">Why Talent Mapping?</h2>
            <p class="text-gray-700 leading-relaxed">
                Melalui Talent Mapping, kamu bisa <strong>mengenal potensi diri lebih dalam, menemukan bidang
                yang paling sesuai dengan kekuatanmu, serta memahami langkah yang tepat untuk mengembangkan
                kemampuan tersebut.</strong> Dengan mengetahui kelebihan sejak dini, kamu dapat belajar dengan
                lebih percaya diri, fokus pada hal yang kamu sukai, dan mempersiapkan masa depan yang lebih terarah.
            </p>
        </div>

        {{-- Placeholder for right side --}}
        <div class="md:w-1/2 flex justify-center z-10">
            <div class="w-64 h-64 bg-[length:8px_8px] bg-[image:repeating-conic-gradient(#e5e5e5_0_25%,white_0_50%)] rounded-lg"></div>
        </div>
    </div>
</section>

{{-- ============ WHAT YOU'LL GET (fully baked asset) ============ --}}
<section>
    <img src="{{ asset('images/landing/what-youll-get.svg') }}" alt="What You'll Get?" class="w-full h-auto block">
</section>

{{-- ============ FOOTER ============ --}}
<footer class="bg-[#1c1b4b] text-white py-16 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-6xl md:text-8xl font-black text-[#f26d3d] mb-12 drop-shadow-md">Talent Mapping</h1>
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-bold text-[#fdb813] mb-4">Sekolah Impian</h2>
                <p class="text-gray-300 mb-6 leading-relaxed">
                    Pondok pendidikan modern yang memadukan kurikulum nasional dengan kurikulum Islam. Terletak di kawasan yang sejuk, asri, dan strategis.
                </p>
                <a href="#" class="inline-block bg-[#3a86ff] text-white font-semibold py-2 px-6 rounded-full hover:bg-blue-600 transition">
                    Baca selengkapnya
                </a>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[#fdb813] mb-4">Kategori Video</h2>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition">Semua</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition">Bahasa</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition">Bidang Al-Qur'an</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition">Bidang Teknologi</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition">Bidang Sosial</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition">Bidang Kreativitas</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

@endsection