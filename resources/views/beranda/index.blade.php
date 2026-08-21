@extends('layouts.app')
@section('title', 'Beranda Ujian')

@section('content')
<div x-data="berandaPage" class="max-w-5xl mx-auto space-y-8">
    {{-- Header --}}
    <div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-slate-900">Ujian Penempatan Bakat</h1>
        <p class="text-sm text-ink/50 mt-1">Pilih paket ujian untuk mengukur kemampuan Anda di bidang Bahasa, IT, dan Karakter.</p>
    </div>

    {{-- Alert Messages --}}
    <div x-show="loading" class="text-sm text-ink/40">Memuat daftar ujian...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    {{-- Exam Category Sections --}}
    <template x-if="!loading && Object.keys(exams).length">
        <div class="space-y-8">
            <template x-for="(list, category) in exams" :key="category">
                <div class="space-y-4">
                    <div class="flex items-center gap-2 border-b border-line pb-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full"
                              :class="{
                                  'bg-brand-blue': category.toLowerCase() === 'bahasa',
                                  'bg-brand-green': category.toLowerCase() === 'it',
                                  'bg-brand-orange': category.toLowerCase() === 'karakter'
                              }"></span>
                        <h2 class="font-display font-bold text-lg text-slate-900" x-text="category"></h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="item in list" :key="item.id">
                            <div class="card p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              :class="'tag-' + category.toLowerCase()" x-text="item.subcategory"></span>
                                        
                                        <template x-if="item.completed">
                                            <span class="bg-emerald-100 text-emerald-700 text-[11px] font-bold px-2 py-0.5 rounded-full">Selesai ✓</span>
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
            </template>
        </div>
    </template>

    <div x-show="!loading && !Object.keys(exams).length" class="card p-8 text-center text-sm text-ink/40">
        Belum ada ujian yang tersedia saat ini.
    </div>
</div>
@endsection