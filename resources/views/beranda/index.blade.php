@extends('layouts.app')
@section('title', 'Beranda Ujian')

@section('content')
<div x-data="berandaPage" class="max-w-5xl mx-auto space-y-8">
    {{-- Header --}}
    <div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-slate-900">Ujian Penempatan Bakat</h1>
        <p class="text-sm text-ink/50 mt-1">Pilih kategori ujian untuk melihat paket tes Bahasa, IT, dan Karakter.</p>
    </div>

    {{-- Alert Messages --}}
    <div x-show="loading" class="text-sm text-ink/40">Memuat daftar ujian...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    {{-- Exam Category Dropdowns --}}
    <template x-if="!loading && orderedCategories.length">
        <div class="space-y-4">
            <template x-for="category in orderedCategories" :key="category">
                <div class="card rounded-2xl overflow-hidden border border-line/70">
                    {{-- Category header (click to expand) --}}
                    <button type="button"
                            @click="toggleCategory(category)"
                            class="w-full flex items-center justify-between gap-4 p-5 text-left hover:bg-slate-50/60 transition-colors"
                            :class="'border-l-4 ' + categoryBorderClass(category)">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0" :class="categoryDotClass(category)"></span>
                            <div class="min-w-0">
                                <h2 class="font-display font-bold text-lg text-slate-900" x-text="category"></h2>
                                <p class="text-xs text-ink/50" x-text="(exams[category]?.length || 0) + ' paket ujian'"></p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-ink/40 shrink-0 transition-transform duration-300"
                             :class="isExpanded(category) ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown content --}}
                    <div x-show="isExpanded(category)"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="border-t border-line bg-white p-4 sm:p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="item in exams[category]" :key="item.id">
                                <div class="rounded-2xl border border-line p-5 flex flex-col justify-between hover:shadow-md transition-shadow bg-slate-50/30">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                                  :class="'tag-' + category.toLowerCase()" x-text="item.subcategory"></span>
                                            <template x-if="item.completed">
                                                <span class="bg-emerald-100 text-emerald-700 text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0">Selesai ✓</span>
                                            </template>
                                        </div>
                                        <h3 class="font-display font-bold text-base text-slate-900" x-text="item.title"></h3>
                                        <p class="text-xs text-ink/60 line-clamp-2" x-text="item.description ?? 'Ujian penempatan kompetensi dasar.'"></p>
                                    </div>

                                    <div class="pt-4 mt-auto">
                                        <a :href="'/ujian/' + item.id"
                                           class="btn-primary w-full text-center text-xs py-2 block"
                                           :class="item.completed ? 'opacity-80 bg-slate-600 hover:bg-slate-700' : ''">
                                            <span x-text="item.completed ? 'Kerjakan Ulang' : 'Mulai Ujian'"></span>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <div x-show="!loading && !orderedCategories.length" class="card p-8 text-center text-sm text-ink/40 rounded-2xl">
        Belum ada ujian yang tersedia saat ini.
    </div>
</div>
@endsection
