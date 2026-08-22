@extends('layouts.app')
@section('title', 'Dashboard Admin / Guru')

@section('content')
<div x-data="adminDashboardPage" class="max-w-6xl mx-auto space-y-6">
    <div x-show="loading" class="text-sm text-ink/40">Memuat daftar santri...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && students.length">
        <div class="space-y-6">
            {{-- Header & ringkasan --}}
            <div class="flex items-end justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="font-display font-bold text-2xl">Admin / Teacher Dashboard</h1>
                    <p class="text-sm text-ink/50">Lihat login, nilai, jumlah test, progress poll, dan portofolio santri.</p>
                </div>
                <span class="text-xs font-medium bg-cloud rounded-full px-3 py-1" x-text="students.length + ' santri'"></span>
            </div>

            {{-- Statistik ringkas dengan animasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card p-5 border-l-4 border-l-brand-blue space-y-1">
                    <p class="text-[11px] uppercase tracking-wide text-ink/40 font-semibold">Total Santri</p>
                    <p class="font-mono font-bold text-2xl" x-data="animatedCounter(summaryTotal)" x-init="start()" x-text="display"></p>
                </div>
                <div class="card p-5 border-l-4 border-l-brand-green space-y-1">
                    <p class="text-[11px] uppercase tracking-wide text-ink/40 font-semibold">Sudah Login / Participated</p>
                    <p class="font-mono font-bold text-2xl text-brand-green" x-data="animatedCounter(summaryParticipated)" x-init="start()" x-text="display"></p>
                </div>
                <div class="card p-5 border-l-4 border-l-brand-orange space-y-1">
                    <p class="text-[11px] uppercase tracking-wide text-ink/40 font-semibold">Total Test Selesai</p>
                    <p class="font-mono font-bold text-2xl text-brand-orange" x-data="animatedCounter(summaryTestsDone)" x-init="start()" x-text="display"></p>
                </div>
            </div>

            {{-- Daftar santri --}}
            <div class="space-y-4">
                <template x-for="student in students" :key="student.id">
                    <article class="card rounded-3xl overflow-hidden border border-line/70 shadow-sm">
                        {{-- Collapsed header — hanya highest score --}}
                        <button type="button" @click="toggleStudent(student.id)" class="w-full text-left p-5 sm:p-6 hover:bg-slate-50/50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-blue text-white flex items-center justify-center font-display font-bold shrink-0"
                                         x-text="studentName(student).charAt(0).toUpperCase()"></div>
                                    <div class="min-w-0">
                                        <h2 class="font-display font-bold text-lg truncate" x-text="studentName(student)"></h2>
                                        <p class="text-sm text-ink/50 truncate" x-text="studentEmail(student)"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 w-full sm:w-auto">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 border-l-4 border-l-brand-blue">
                                        <p class="text-[11px] uppercase tracking-wide text-ink/40">Login</p>
                                        <p class="font-mono font-bold text-lg" x-data="animatedCounter(loginCount(student))" x-init="start()" x-text="display"></p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 border-l-4 border-l-brand-green">
                                        <p class="text-[11px] uppercase tracking-wide text-ink/40">Test Done</p>
                                        <p class="font-mono font-bold text-lg" x-data="animatedCounter(testsDone(student))" x-init="start()" x-text="display"></p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 border-l-4 border-l-brand-orange">
                                        <p class="text-[11px] uppercase tracking-wide text-ink/40">Highest Score</p>
                                        <p class="font-mono font-bold text-lg text-brand-green">
                                            <span x-data="animatedCounter(highestScore(student))" x-init="start()" x-text="display"></span>%
                                        </p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 flex items-center justify-between gap-2">
                                        <div>
                                            <p class="text-[11px] uppercase tracking-wide text-ink/40">Portfolio</p>
                                            <p class="font-mono font-bold text-lg" x-text="portfolioFiles(student).length"></p>
                                        </div>
                                        <svg class="w-5 h-5 text-ink/30 shrink-0 transition-transform duration-300"
                                             :class="expanded[student.id] ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </button>

                        {{-- Expanded detail --}}
                        <div x-show="expanded[student.id]"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-cloak
                             class="border-t border-line bg-white">
                            <div class="p-5 sm:p-6 space-y-6">
                                {{-- Poll per kategori — bar dari kiri --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <template x-for="group in revealBars(student)" :key="group.label">
                                        <section class="card rounded-3xl p-4 space-y-3"
                                                 :class="categoryBorderClass(group.label)">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-display font-bold text-sm" x-text="group.label"></h3>
                                                <span class="text-[11px] font-semibold text-ink/40" x-text="group.items.length + ' test(s)'"></span>
                                            </div>

                                            <div class="space-y-3" x-show="group.items.length">
                                                <template x-for="item in group.items" :key="item.title">
                                                    <div class="space-y-1">
                                                        <div class="flex items-center justify-between text-xs text-ink/60">
                                                            <span class="truncate pr-2" x-text="item.title"></span>
                                                            <span class="font-mono shrink-0" x-text="item.value + '%'"></span>
                                                        </div>
                                                        <div class="w-full h-2.5 rounded-full border border-slate-200 p-0.5 bg-transparent overflow-hidden">
                                                            <div class="h-full rounded-full transition-all duration-700 ease-out"
                                                                 :class="categoryBarClass(group.label)"
                                                                 :style="barWidth(student.id, item.value)"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                            <p x-show="!group.items.length" class="text-xs text-ink/40">Belum ada test di kategori ini.</p>
                                        </section>
                                    </template>
                                </div>

                                {{-- Portfolio --}}
                                <section class="card rounded-3xl p-4 space-y-3 border-l-4 border-l-brand-orange">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-display font-bold text-sm">Portfolio Uploaded</h3>
                                        <span class="text-[11px] font-semibold text-ink/40" x-text="portfolioFiles(student).length + ' file(s)'"></span>
                                    </div>

                                    <div x-show="student.portfolio?.links" class="text-sm text-ink/60">
                                        <span class="font-semibold text-ink/40 text-xs uppercase">Links: </span>
                                        <span x-text="student.portfolio.links"></span>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="file in portfolioFiles(student)" :key="file">
                                            <span class="px-3 py-1.5 rounded-full bg-slate-100 text-xs text-ink/70" x-text="file"></span>
                                        </template>
                                        <span x-show="!portfolioFiles(student).length" class="text-sm text-ink/40">Belum ada portofolio.</span>
                                    </div>
                                </section>

                                <div class="flex justify-end">
                                    <a :href="'/admin/koreksi/' + student.id" class="btn-primary text-sm px-4 py-2">Buka Koreksi Essay</a>
                                </div>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </div>
    </template>

    <div x-show="!loading && !error && !students.length" class="card p-8 text-center text-sm text-ink/40 rounded-3xl">
        Belum ada santri terdaftar.
    </div>
</div>
@endsection
