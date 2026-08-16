@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div x-data="berandaPage" class="max-w-5xl mx-auto space-y-10">

    <section class="card p-6">
        <p class="text-sm text-ink/50">Hello!</p>
        <h2 class="font-display font-bold text-2xl mt-1">Welcome, <span x-text="user?.name ?? 'Santri'"></span></h2>
        <p class="text-sm text-ink/50 mt-2">Pilih kategori lalu mulai tes untuk melihat kecocokanmu.</p>
    </section>

    <section class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
            <div>
                <h2 class="font-display font-bold text-xl">Daftar Ujian</h2>
                <p class="text-sm text-ink/50 mt-1">Pilih ujian untuk mengetahui kelas yang paling cocok dengan bakatmu.</p>
            </div>
        </div>

        <div x-show="loading" class="text-sm text-ink/40">Memuat daftar ujian...</div>
        <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

        <template x-for="kat in kategori" :key="kat.slug">
            <div :id="'kategori-' + kat.slug" class="card scroll-mt-24 overflow-hidden transition-shadow"
                 :class="isCategoryComplete(kat) ? 'ring-2 ring-brand-green border-brand-green' : ''">
                <button type="button"
                        @click="openCategory = openCategory === kat.slug ? null : kat.slug"
                        :aria-expanded="openCategory === kat.slug"
                        class="w-full flex items-center justify-between gap-4 p-5 text-left hover:bg-cloud transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full" :class="'bg-brand-' + kat.warna"></span>
                        <span>
                            <span class="block font-display font-semibold text-base" x-text="kat.nama"></span>
                            <span class="block text-sm text-ink/50 mt-0.5" x-text="kat.ujian.length + ' pilihan tes'"></span>
                        </span>
                    </span>
                    <span class="flex items-center gap-2">
                        <svg x-show="isCategoryComplete(kat)" class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                        <svg class="w-5 h-5 shrink-0 text-ink/40 transition-transform" :class="openCategory === kat.slug && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </button>
                <div x-show="openCategory === kat.slug" x-transition x-cloak class="border-t border-line p-4 sm:p-5">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="ujian in kat.ujian" :key="ujian.id ?? ujian.subcategory">
                            <a :href="(ujian.subcategory ?? '').toLowerCase() === 'portofolio' ? '/portofolio' : '/ujian/' + ujian.id"
                               class="card p-4 flex items-center justify-between hover:shadow-md transition-all group"
                               :class="ujian.completed ? 'ring-1 ring-brand-green border-brand-green' : ''">
                                <div>
                                    <p class="font-medium text-sm" x-text="ujian.title ?? ujian.subcategory"></p>
                                    <p class="text-xs mt-1 inline-flex px-2 py-0.5 rounded-full"
                                       :class="'tag-' + kat.slug"
                                       x-text="ujian.subcategory ?? ''"></p>
                                </div>
                                <svg x-show="ujian.completed" class="w-5 h-5 shrink-0 text-brand-green" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                                <svg x-show="!ujian.completed" class="w-4 h-4 text-ink/30 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </section>
</div>
@endsection
