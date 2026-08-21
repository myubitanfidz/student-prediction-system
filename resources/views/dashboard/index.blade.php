@extends('layouts.app')
@section('title', 'Profil & Hasil Prediksi')

@section('content')
<div x-data="profilePage" class="max-w-5xl mx-auto space-y-6">
    <div x-show="loading" class="text-sm text-ink/40">Memuat profil dan statistik...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && profile">
        <div class="space-y-6">
            {{-- User Identity Header --}}
            <div class="card p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-brand-blue text-white text-xl font-display font-bold flex items-center justify-center"
                         x-text="profile.name.charAt(0).toUpperCase()"></div>
                    <div>
                        <h1 class="font-display font-bold text-xl text-slate-900" x-text="profile.name"></h1>
                        <p class="text-sm text-ink/50" x-text="profile.email"></p>
                        <span class="inline-block mt-1 text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-700"
                              x-text="'Role: ' + profile.role"></span>
                    </div>
                </div>
                <a href="{{ route('portofolio.index') }}" class="btn-primary text-xs px-4 py-2 self-start sm:self-auto">
                    Kelola Portofolio
                </a>
            </div>

            {{-- Stat & Prediction Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Prediksi Bahasa --}}
                <div class="card p-5 border-l-4 border-l-brand-blue space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink/40">Prediksi Level Bahasa</p>
                    <div class="flex items-baseline gap-2">
                        <span class="font-display font-bold text-3xl text-brand-blue" x-text="languageLevel"></span>
                        <span class="text-xs text-ink/50" x-text="'(Akurasi ' + (languageAccuracy || 0) + '%)'"></span>
                    </div>
                    <p class="text-xs text-ink/60">Berdasarkan hasil tes Bahasa Inggris &amp; Arab.</p>
                </div>

                {{-- Prediksi IT --}}
                <div class="card p-5 border-l-4 border-l-brand-green space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink/40">Prediksi Level IT</p>
                    <div class="flex items-baseline gap-2">
                        <span class="font-display font-bold text-3xl text-brand-green" x-text="itLevel"></span>
                        <span class="text-xs text-ink/50" x-text="'(Akurasi ' + (itAccuracy || 0) + '%)'"></span>
                    </div>
                    <p class="text-xs text-ink/60">Berdasarkan logika coding, desain, &amp; multimedia.</p>
                </div>

                {{-- Portofolio Status --}}
                <div class="card p-5 border-l-4 border-l-brand-orange space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink/40">Status Portofolio</p>
                    <div class="font-display font-bold text-2xl"
                         :class="portfolio ? 'text-brand-green' : 'text-slate-400'"
                         x-text="portfolio ? 'Sudah Diunggah' : 'Belum Ada'"></div>
                    <p class="text-xs text-ink/60" x-text="portfolio?.files?.length ? (portfolio.files.length + ' berkas karya tersimpan') : 'Tautkan link atau unggah file karya'"></p>
                </div>
            </div>

            {{-- Detail Statistik Nilai --}}
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-line">
                    <h2 class="font-display font-bold text-base text-slate-900">Rekap Nilai Per Ujian</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-line text-[11px] font-semibold uppercase tracking-wide text-ink/40 text-left">
                            <tr>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Ujian</th>
                                <th class="px-4 py-3 text-center">Soal Dijawab</th>
                                <th class="px-4 py-3 text-center">Akurasi PG</th>
                                <th class="px-4 py-3 text-right">Total Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-for="stat in stats" :key="stat.exam_id">
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-semibold text-xs text-brand-blue" x-text="stat.category"></td>
                                    <td class="px-4 py-3 font-medium" x-text="stat.exam_title"></td>
                                    <td class="px-4 py-3 text-center" x-text="stat.answered_count"></td>
                                    <td class="px-4 py-3 text-center font-mono" x-text="(stat.mc_accuracy_pct || 0) + '%'"></td>
                                    <td class="px-4 py-3 text-right font-mono font-bold" x-text="stat.total_score ?? '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection