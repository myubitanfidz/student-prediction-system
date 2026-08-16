@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div x-data="profilePage" class="max-w-5xl mx-auto space-y-10">
    <div x-show="loading" class="text-sm text-ink/40">Memuat profile...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && student">
        <div class="space-y-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-brand-blue text-white text-lg font-display font-bold flex items-center justify-center"
                     x-text="student.name.charAt(0).toUpperCase()"></div>
                <div>
                    <h1 class="font-display font-bold text-2xl" x-text="student.name"></h1>
                    <p class="text-sm text-ink/50">Ringkasan kecocokan berdasarkan jawaban tesmu.</p>
                </div>
            </div>

            <section>
                <h2 class="font-display font-bold text-xl mb-1">Bahasa</h2>
                <p class="text-sm text-ink/50 mb-4">Level CEFR dari A1 (dasar) sampai C2 (sangat mahir).</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <template x-for="item in languageStats" :key="item.exam_id">
                        <article class="card p-5 flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-display font-semibold text-sm" x-text="item.exam_title"></h3>
                                <p class="text-xs text-ink/50 mt-1" x-text="item.correct_count + ' benar dari ' + item.question_count + ' soal pilihan ganda'"></p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-display font-bold text-2xl text-brand-blue" x-text="languageLevel(item.nilaiPersen)"></p>
                                <p class="font-mono text-xs text-ink/50" x-text="item.nilaiPersen + '%'"></p>
                            </div>
                        </article>
                    </template>
                </div>
            </section>

            <section>
                <h2 class="font-display font-bold text-xl mb-1">IT</h2>
                <p class="text-sm text-ink/50 mb-4">Rookie: perlu banyak latihan; Amateur: punya potensi; Pro: sangat cocok.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    <template x-for="item in itStats" :key="item.exam_id">
                        <article class="card p-4 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-display font-semibold text-sm leading-snug" x-text="item.exam_title"></h3>
                                    <p class="text-xs text-ink/50 mt-1" x-text="item.correct_count + ' benar dari ' + item.question_count + ' soal'"></p>
                                </div>
                                <span class="font-mono text-base font-semibold text-brand-green" x-text="item.nilaiPersen + '%'"></span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="tag-it inline-flex px-2 py-0.5 rounded-full text-xs font-medium" x-text="itLevel(item.nilaiPersen)"></span>
                                <div class="h-1.5 flex-1 rounded-full bg-line overflow-hidden"><div class="h-full rounded-full bg-brand-green" :style="'width: ' + item.nilaiPersen + '%'"></div></div>
                            </div>
                        </article>
                    </template>
                </div>
            </section>

            <section>
                <h2 class="font-display font-bold text-xl mb-4">Karakter</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <template x-for="item in characterStats" :key="item.exam_id">
                        <article class="card p-4 flex items-center justify-between gap-4">
                            <div><h3 class="font-display font-semibold text-sm" x-text="item.exam_title"></h3><p class="text-xs text-ink/50 mt-1" x-text="item.correct_count + ' benar dari ' + item.question_count + ' soal'"></p></div>
                            <span class="font-mono font-semibold text-brand-orange" x-text="item.nilaiPersen + '%'"></span>
                        </article>
                    </template>
                </div>
            </section>

            <section>
                <h2 class="font-display font-bold text-xl mb-4">Portofolio</h2>
                <div class="card p-6 space-y-4" x-show="portfolio">
                    <div><p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Link</p><p class="text-sm break-all" x-text="portfolio?.links || 'Belum ada link.'"></p></div>
                    <div><p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-2">Berkas</p><ul class="space-y-1.5"><template x-for="file in (portfolio?.files ?? [])" :key="file"><li><a :href="file" target="_blank" class="text-sm text-brand-green hover:underline break-all" x-text="file"></a></li></template><li x-show="!(portfolio?.files ?? []).length" class="text-sm text-ink/40">Belum ada berkas.</li></ul></div>
                </div>
                <div x-show="!portfolio" class="card p-6 text-sm text-ink/40">Kamu belum mengirim portofolio.</div>
            </section>
        </div>
    </template>
</div>
@endsection
