@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div x-data="dashboardPage" class="max-w-5xl mx-auto space-y-10">

    <div x-show="loading" class="text-sm text-ink/40">Memuat dashboard...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && student">
        <div class="space-y-10">
            {{-- Nama santri --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-brand-blue text-white text-lg font-display font-bold flex items-center justify-center"
                     x-text="student.name.charAt(0).toUpperCase()"></div>
                <div>
                    <h1 class="font-display font-bold text-2xl" x-text="student.name"></h1>
                    <p class="text-sm text-ink/50">Rekap nilai ujian dan portofolio kamu.</p>
                </div>
            </div>

            {{-- Lima rekomendasi berdasarkan jawaban pilihan ganda yang benar --}}
            <section>
                <h2 class="font-display font-bold text-xl mb-1">Kecocokan Kelas</h2>
                <p class="text-sm text-ink/50 mb-4">Lima bidang dengan kecocokan tertinggi dari jawaban pilihan gandamu.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">
                    <div x-show="!stats.length" class="card p-5 text-sm text-ink/40 sm:col-span-2 xl:col-span-5">Belum ada ujian yang dikerjakan.</div>
                    <template x-for="item in stats" :key="item.exam_id">
                        <article class="card p-4 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full" :class="'tag-' + item.category.toLowerCase()" x-text="item.category"></span>
                                <span class="font-mono text-lg font-semibold" x-text="item.nilaiPersen + '%'"></span>
                            </div>
                            <div>
                                <h3 class="font-display font-semibold text-sm leading-snug" x-text="item.exam_title"></h3>
                                <p class="text-xs text-ink/50 mt-1" x-text="item.correct_count + ' benar dari ' + item.question_count + ' soal'"></p>
                            </div>
                            <div class="h-1.5 rounded-full bg-line overflow-hidden">
                                <div class="h-full rounded-full" :class="'bg-brand-' + item.warna" :style="`width: ${item.nilaiPersen}%`"></div>
                            </div>
                        </article>
                    </template>
                </div>
            </section>

            {{-- Portofolio --}}
            <section>
                <h2 class="font-display font-bold text-xl mb-4">Portofolio</h2>
                <div class="card p-6 space-y-4" x-show="portfolio">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Link</p>
                        <p class="text-sm break-all" x-text="portfolio?.links || 'Belum ada link.'"></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-2">Berkas</p>
                        <ul class="space-y-1.5">
                            <template x-for="file in (portfolio?.files ?? [])" :key="file">
                                <li>
                                    <a :href="file" target="_blank" class="text-sm text-brand-green hover:underline break-all" x-text="file"></a>
                                </li>
                            </template>
                            <li x-show="!(portfolio?.files ?? []).length" class="text-sm text-ink/40">Belum ada berkas.</li>
                        </ul>
                    </div>
                </div>
                <div x-show="!portfolio" class="card p-6 text-sm text-ink/40">Kamu belum mengirim portofolio.</div>
            </section>
        </div>
    </template>
</div>
@endsection
