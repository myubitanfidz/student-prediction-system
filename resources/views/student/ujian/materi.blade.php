@extends('layouts.app')
@section('title', 'Materi — Talent Mapping')

@php
    $activeMateri = request()->route('slug') ?? request()->query('materi', 'arab');
    
    $materiConfig = [
        'arab' => [
            'label'        => 'Arabic',
            'photo'        => asset('images/landing/materi/arabCard.jpg'),
            'placeholder'  => false,
            'highlight'    => '#FDCB6E',
            'headline_pre' => 'Selamat datang di tempat',
            'headline_hi'  => 'satu bahasa membuka banyak kesempatan',
            'description'  => "Bahasa Arab adalah bahasa yang digunakan untuk memahami berbagai sumber keislaman, termasuk Al-Qur'an dan hadis. Dengan mempelajarinya, kamu dapat memahami makna yang terkandung di balik setiap kata dan ayat secara lebih mendalam.",
            'benefits'     => 'Membantu membuka pintu ke ranah global, memperluas akses informasi dan literatur dunia, meningkatkan ketajaman logika gramatikal, serta membekali kamu dengan berbagai budaya dan menangkap peluang akademik maupun karier di tingkat internasional.',
            'apps_label'   => 'Cara Belajar',
            'apps_color'   => '#00CEC9',
            'apps'         => ['Al-Maany', 'Duolingo', 'Google Translate', 'Tashreef App'],
        ],
        'inggris' => [
            'label'        => 'English',
            'photo'        => asset('images/landing/materi/inggris.jpg'),
            'placeholder'  => false,
            'highlight'    => '#FDCB6E',
            'headline_pre' => 'Selamat datang di tempat',
            'headline_hi'  => 'bahasa dan makna bertemu',
            'description'  => 'Bahasa Inggris adalah kunci utama akses informasi global, sains, dan teknologi masa kini. Kuasai grammar, vocabulary, dan speaking untuk bersaing di era digital.',
            'benefits'     => 'Membuka wawasan internasional, mempermudah adaptasi dengan software dan dokumentasi teknologi, serta meningkatkan daya saing karier di masa depan.',
            'apps_label'   => 'Cara Belajar',
            'apps_color'   => '#00CEC9',
            'apps'         => ['Grammarly', 'Duolingo', 'Cambridge Dictionary', 'ELSA Speak'],
        ],
        'programming' => [
            'label'        => 'Programming',
            'photo'        => asset('images/landing/materi/programmingCard.jpg'),
            'placeholder'  => false,
            'highlight'    => '#74b9ff',
            'headline_pre' => 'Selamat datang di dunia, tempat',
            'headline_hi'  => 'ide berubah menjadi teknologi',
            'description'  => 'Programming adalah bidang yang mempelajari cara memberikan instruksi kepada komputer untuk menyelesaikan berbagai masalah menggunakan logika, kreativitas, dan komputasi.',
            'benefits'     => 'Melatih kemampuan computational thinking, pemecahan masalah sistematis, dan membuka peluang karir global di bidang rekayasa teknologi.',
            'apps_label'   => 'Aplikasi Pendukung',
            'apps_color'   => '#E17055',
            'apps'         => ['VS Code', 'Git / GitHub', 'Python', 'Node.js'],
        ],
        'dkv' => [
            'label'        => 'DKV',
            'photo'        => asset('images/landing/materi/dkvCard.jpg'),
            'placeholder'  => false,
            'highlight'    => '#55efc4',
            'headline_pre' => 'Selamat datang di dunia, di mana',
            'headline_hi'  => 'visual mampu bercerita',
            'description'  => 'Pelajari harmoni tata letak (layout), psikologi warna, tipografi, dan komposisi grafis untuk mengomunikasikan pesan yang berdampak luas.',
            'benefits'     => 'Mengasah kreativitas estetis, penguasaan branding visual, serta kemampuan komunikasi visual digital.',
            'apps_label'   => 'Aplikasi Pendukung',
            'apps_color'   => '#E17055',
            'apps'         => ['Adobe Photoshop', 'Figma', 'Adobe Illustrator', 'Canva'],
        ],
        'videografi' => [
            'label'        => 'Videografi',
            'photo'        => asset('images/landing/materi/videografiCard.jpg'),
            'placeholder'  => false, 
            'highlight'    => '#a29bfe',
            'headline_pre' => 'Selamat datang di tempat momen',
            'headline_hi'  => 'diabadikan dan cerita dihidupkan',
            'description'  => 'Videografi adalah bidang yang berfokus pada menyampaikan cerita atau pesan melalui gambar bergerak — mulai dari pengambilan gambar, komposisi, audio, storytelling, hingga proses editing.',
            'benefits'     => 'Mengembangkan insting storytelling, pemahaman produksi sinema, dan keahlian di industri media kreatif.',
            'apps_label'   => 'Aplikasi Pendukung',
            'apps_color'   => '#E17055',
            'apps'         => ['Premiere Pro', 'DaVinci Resolve', 'CapCut', 'After Effects'],
        ],
        'komik' => [
            'label'        => 'komik',
            'photo'        => asset('images/landing/materi/komikCard.jpg'),
            'placeholder'  => false, 
            'highlight'    => '#a29bfe',
            'headline_pre' => 'Selamat datang di tempat',
            'headline_hi'  => 'imajinasi bertemu dengan cerita.',
            'description'  => 'Komik menggabungkan gambar dan storytelling untuk menciptakan sebuah cerita. Kamu akan belajar mengembangkan karakter, alur cerita, dll hingga menyusun panel agar pembaca dapat mengikuti cerita dengan mudah.',
            'benefits'     => '............ Masih menunggu ya',
            'apps_label'   => 'Aplikasi Pendukung',
            'apps_color'   => '#E17055',
            'apps'         => ['Ibis Paint', 'Procreate', 'Clip Studio Paint', 'Medibang'],
        ],
    ];

    $m = $materiConfig[$activeMateri] ?? null;
@endphp

@section('content')
@if ($m)
<div x-data="materiPage()" class="h-screen overflow-hidden bg-[#f0f3f8] py-6 px-4 relative flex flex-col items-center">
    
    {{-- The Yellow Card Structure (WIDTH IS NOW CHUNKY: max-w-[450px]) --}}
    <div class="relative max-w-[450px] w-full mx-auto mt-6 transform scale-85 origin-top">

        {{-- "Klip" kertas di atas --}}
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-11 h-6 bg-[#FFC107] rounded-t-full z-10"></div>

        {{-- Bingkai kuning --}}
        <div class="bg-[#FFC107] rounded-[28px] p-2.5 shadow-2xl">
            <div class="bg-white rounded-[22px] overflow-hidden relative">

                {{-- Tombol X (Close) --}}
                <a href="{{ request()->fullUrlWithQuery(['materi' => null]) }}"
                   class="absolute top-3 right-3 z-20 w-7 h-7 flex items-center justify-center rounded-full bg-white/80 backdrop-blur-xs text-slate-500 hover:text-slate-900 hover:bg-white font-bold text-sm shadow-sm transition">
                    ✕
                </a>

                {{-- Foto / placeholder (HEIGHT IS SHORTER: h-32 sm:h-36) --}}
                <div class="relative">
                    @if ($m['placeholder'])
                        <div class="w-full h-22 sm:h-36 bg-[length:16px_16px] bg-[image:repeating-conic-gradient(#e5e5e5_0_25%,white_0_50%)]"></div>
                    @else
                        <img src="{{ $m['photo'] }}" alt="{{ $m['label'] }}" class="w-full h-32 sm:h-36 object-cover">
                    @endif
                    <span class="absolute bottom-3 right-3 bg-[#E74C3C] text-white text-[11px] font-bold px-3.5 py-1.5 rounded-full shadow-md">
                        {{ $m['label'] }}
                    </span>
                </div>

                {{-- Content (TIGHT SPACING: p-4, space-y-2) --}}
                <div class="p-4 sm:p-5 space-y-2">
                    <h3 class="font-display font-black text-xl sm:text-2xl text-[#0984E3] leading-snug">
                        {{ $m['headline_pre'] }}
                        <span class="px-1 underline decoration-2 text-slate-900" style="background-color: {{ $m['highlight'] }}">{{ $m['headline_hi'] }}</span>.
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">{{ $m['description'] }}</p>

                    <div class="border border-dashed border-slate-400 rounded-2xl p-3 sm:p-4 space-y-1">
                        <h4 class="font-display font-extrabold text-sm sm:text-base text-[#E17055]">Manfaat</h4>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">{{ $m['benefits'] }}</p>
                    </div>

                    <div class="space-y-2.5">
                        <h4 class="font-display font-extrabold text-sm sm:text-base text-[#E17055]">{{ $m['apps_label'] }}</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($m['apps'] as $app)
                                <span class="text-white text-xs font-semibold px-4 py-1.5 rounded-full" style="background-color: {{ $m['apps_color'] }}">{{ $app }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200 flex justify-end">
                        <button type="button" @click="mulaiQuiz('{{ $activeMateri }}')"
                                class="bg-[#0984E3] hover:bg-[#0773c5] text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl transition shadow active:scale-95">
                            Mulai Ujian Materi Ini →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('materiPage', () => ({
        async mulaiQuiz(targetKey) {
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
                
                const category = ['arab', 'inggris'].includes(targetKey) ? 'bahasa' : 'it';

                if (category === 'it') {
                    selectedExam = examPool.find(e => e.home_slot === 'it_gclwama' && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'it' && isExamActive(e))
                                || examPool.find(e => (e.subcategory || '').toLowerCase().includes('gclwama') && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'it');
                } else {
                    const targetSlot = targetKey.includes('arab') ? 'bahasa_arab' : 'bahasa_inggris';
                    selectedExam = examPool.find(e => e.home_slot === targetSlot && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'bahasa' && (e.subcategory || e.title || '').toLowerCase().includes(targetKey) && isExamActive(e))
                                || examPool.find(e => (e.category || '').toLowerCase() === 'bahasa' && isExamActive(e));
                }

                if (selectedExam && selectedExam.id) {
                    window.location.href = `/ujian/${selectedExam.id}`;
                } else {
                    alert(`Paket ujian untuk materi ${targetKey.toUpperCase()} belum diaktifkan oleh admin.`);
                }
            } catch (err) {
                console.error('Fetch exams error:', err);
                alert('Gagal menghubungi server untuk memuat ujian.');
            }
        }
    }));
});
</script>
@endif
@endsection