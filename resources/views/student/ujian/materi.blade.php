{{--
    resources/views/materi.blade.php

    Semua 5 popup materi digabung jadi SATU file ini (bukan file terpisah
    lagi). Kartu mana yang muncul ditentukan oleh $activeMateri (dari query
    string ?materi=... di explore.blade.php).

    Cara nambah/ubah materi baru: tinggal tambah/ubah satu entri di array
    $materiConfig di bawah — tidak perlu bikin file baru.
--}}
@php
    $materiConfig = [
        'arab' => [
            'label'        => 'Arabic',
            'photo'        => asset('images/materi/arab.jpg'),
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
            'photo'        => asset('images/materi/inggris.jpg'),
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
            'photo'        => asset('images/materi/programming.jpg'),
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
            'photo'        => asset('images/materi/dkv.jpg'),
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
            'photo'        => null,
            'placeholder'  => true, // tidak ada foto asli, sama seperti gambar referensi
            'highlight'    => '#a29bfe',
            'headline_pre' => 'Selamat datang di tempat momen',
            'headline_hi'  => 'diabadikan dan cerita dihidupkan',
            'description'  => 'Videografi adalah bidang yang berfokus pada menyampaikan cerita atau pesan melalui gambar bergerak — mulai dari pengambilan gambar, komposisi, audio, storytelling, hingga proses editing.',
            'benefits'     => 'Mengembangkan insting storytelling, pemahaman produksi sinema, dan keahlian di industri media kreatif.',
            'apps_label'   => 'Aplikasi Pendukung',
            'apps_color'   => '#E17055',
            'apps'         => ['Premiere Pro', 'DaVinci Resolve', 'CapCut', 'After Effects'],
        ],
    ];

    $m = $materiConfig[$activeMateri] ?? null;
@endphp

@if ($m)
    <div class="relative max-w-md w-full">

        {{-- "Klip" kertas di atas — didekati pakai CSS, bukan SVG --}}
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-11 h-6 bg-[#FFC107] rounded-t-full z-10"></div>

        {{-- Bingkai kuning --}}
        <div class="bg-[#FFC107] rounded-[28px] p-2.5 shadow-2xl">
            <div class="bg-white rounded-[22px] overflow-hidden relative">

                {{-- Tombol X — kode biasa, bukan SVG, jadi bisa diklik & responsif --}}
                <a href="{{ request()->fullUrlWithQuery(['' => null]) }}"
                   class="absolute top-3 right-3 z-20 w-7 h-7 flex items-center justify-center rounded-full bg-white/80 backdrop-blur-xs text-slate-500 hover:text-slate-900 hover:bg-white font-bold text-sm shadow-sm transition">
                    ✕
                </a>

                {{-- Foto / placeholder --}}
                <div class="relative">
                    @if ($m['placeholder'])
                        {{-- PLACEHOLDER — belum ada foto asli, ganti jadi <img> nanti --}}
                        <div class="w-full h-40 sm:h-48 bg-[length:16px_16px] bg-[image:repeating-conic-gradient(#e5e5e5_0_25%,white_0_50%)]"></div>
                    @else
                        <img src="{{ $m['photo'] }}" alt="{{ $m['label'] }}" class="w-full h-40 sm:h-48 object-cover">
                    @endif
                    <span class="absolute bottom-3 right-3 bg-[#E74C3C] text-white text-[11px] font-bold px-3.5 py-1.5 rounded-full shadow-md">
                        {{ $m['label'] }}
                    </span>
                </div>

                <div class="p-5 sm:p-7 space-y-4">
                    <h3 class="font-display font-black text-xl sm:text-2xl text-[#0984E3] leading-snug">
                        {{ $m['headline_pre'] }}
                        <span class="px-1 underline decoration-2 text-slate-900" style="background-color: {{ $m['highlight'] }}">{{ $m['headline_hi'] }}</span>.
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">{{ $m['description'] }}</p>

                    <div class="border border-dashed border-slate-400 rounded-2xl p-4 sm:p-5 space-y-1.5">
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

                    <div class="pt-3 border-t border-slate-200 flex justify-end">
                        <button type="button" @click="mulaiQuiz('{{ $activeMateri }}')"
                                class="bg-[#0984E3] hover:bg-[#0773c5] text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl transition shadow active:scale-95">
                            Mulai Ujian Materi Ini →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
