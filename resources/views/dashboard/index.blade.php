@extends('layouts.app')
@section('title', 'Profil & Hasil Prediksi')

@section('content')
<div x-data="profilePage" class="max-w-5xl mx-auto space-y-6">
    <div x-show="loading" class="text-sm text-ink/40">Memuat profil dan statistik...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && profile">
        <div class="space-y-6">
            {{-- Horizontal Progress Bar Total Pengerjaan Soal (Hanya Garis Pinggir & Terisi Sesuai Persen) --}}
            <div class="card p-5 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-sm">
                    <div>
                        <span class="font-display font-bold text-slate-900">Total Progres Pengerjaan Ujian</span>
                        <span class="text-xs text-ink/50 block sm:inline sm:ml-2"
                              x-text="`(${totalAnswered} dari ${totalQuestionsCount} soal terjawab)`"></span>
                    </div>
                    <span class="font-mono font-bold text-base text-brand-blue" x-text="overallProgress + '%'"></span>
                </div>
                {{-- Bar horizontal: outline transparan/border tegas, isi solid --}}
                <div class="w-full h-3.5 rounded-full border-2 border-slate-300 p-0.5 bg-transparent overflow-hidden">
                    <div class="h-full rounded-full bg-brand-blue transition-all duration-500 ease-out"
                         :style="`width: ${overallProgress}%`"></div>
                </div>
            </div>

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

            {{-- Stat & Prediction Cards dengan Bar Garis Pinggir --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Prediksi Bahasa --}}
                <div class="card p-5 border-l-4 border-l-brand-blue space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink/40">Prediksi Bahasa</p>
                        <span class="font-display font-bold text-xl text-brand-blue" x-text="languageLevel"></span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-ink/60 font-medium">
                            <span>Akurasi Jawaban</span>
                            <span class="font-mono" x-text="(languageAccuracy || 0) + '%'"></span>
                        </div>
                        <div class="w-full h-2.5 rounded-full border border-brand-blue/40 p-0.5 bg-transparent overflow-hidden">
                            <div class="h-full rounded-full bg-brand-blue transition-all duration-500"
                                 :style="`width: ${languageAccuracy || 0}%`"></div>
                        </div>
                    </div>
                    <p class="text-xs text-ink/50">Evaluasi tes Bahasa Inggris &amp; Arab.</p>
                </div>

                {{-- Prediksi IT --}}
                <div class="card p-5 border-l-4 border-l-brand-green space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink/40">Prediksi IT</p>
                        <span class="font-display font-bold text-xl text-brand-green" x-text="itLevel"></span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-ink/60 font-medium">
                            <span>Akurasi Jawaban</span>
                            <span class="font-mono" x-text="(itAccuracy || 0) + '%'"></span>
                        </div>
                        <div class="w-full h-2.5 rounded-full border border-brand-green/40 p-0.5 bg-transparent overflow-hidden">
                            <div class="h-full rounded-full bg-brand-green transition-all duration-500"
                                 :style="`width: ${itAccuracy || 0}%`"></div>
                        </div>
                    </div>
                    <p class="text-xs text-ink/50">Evaluasi coding, desain &amp; multimedia.</p>
                </div>

                {{-- Portofolio Status --}}
                <div class="card p-5 border-l-4 border-l-brand-orange space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink/40">Portofolio</p>
                        <span class="font-display font-bold text-sm"
                              :class="portfolio ? 'text-brand-green' : 'text-slate-400'"
                              x-text="portfolio ? 'Lengkap ✓' : 'Belum Ada'"></span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-ink/60 font-medium">
                            <span>Berkas Karya</span>
                            <span class="font-mono" x-text="`${portfolio?.files?.length || 0} / 5`"></span>
                        </div>
                        <div class="w-full h-2.5 rounded-full border border-brand-orange/40 p-0.5 bg-transparent overflow-hidden">
                            <div class="h-full rounded-full bg-brand-orange transition-all duration-500"
                                 :style="`width: ${((portfolio?.files?.length || 0) / 5) * 100}%`"></div>
                        </div>
                    </div>
                    <p class="text-xs text-ink/50" x-text="portfolio?.links ? 'Link repositori/karya terhubung' : 'Tautkan link atau unggah file'"></p>
                </div>
            </div>

            {{-- Detail Statistik Nilai dengan Bar Progres di Tiap Baris --}}
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-line">
                    <h2 class="font-display font-bold text-base text-slate-900">Rekap Nilai &amp; Pengerjaan Per Ujian</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-line text-[11px] font-semibold uppercase tracking-wide text-ink/40 text-left">
                            <tr>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Ujian</th>
                                <th class="px-4 py-3 text-center">Soal Dijawab</th>
                                <th class="px-4 py-3 min-w-[160px]">Akurasi PG (Bar)</th>
                                <th class="px-4 py-3 text-right">Total Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-for="stat in stats" :key="stat.exam_id">
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-semibold text-xs text-brand-blue" x-text="stat.category"></td>
                                    <td class="px-4 py-3 font-medium" x-text="stat.exam_title"></td>
                                    <td class="px-4 py-3 text-center font-mono" x-text="stat.answered_count"></td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[11px] font-mono text-ink/60">
                                                <span>Akurasi</span>
                                                <span x-text="(stat.mc_accuracy_pct || 0) + '%'"></span>
                                            </div>
                                            <div class="w-full h-2 rounded-full border border-slate-300 p-0.5 bg-transparent overflow-hidden">
                                                <div class="h-full rounded-full bg-brand-blue transition-all"
                                                     :style="`width: ${stat.mc_accuracy_pct || 0}%`"></div>
                                            </div>
                                        </div>
                                    </td>
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