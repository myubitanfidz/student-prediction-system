@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div x-data="berandaPage" class="max-w-5xl mx-auto space-y-10">

    {{-- Profil / akun --}}
    <section id="profil" class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="w-16 h-16 rounded-full bg-brand-blue text-white text-xl font-display font-bold flex items-center justify-center shrink-0"
                 x-text="(user?.name ?? 'S').charAt(0).toUpperCase()"></div>
            <div class="flex-1 grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Nama</p>
                    <p class="font-medium" x-text="user?.name ?? '—'"></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Email</p>
                    <p class="font-medium" x-text="user?.email ?? '—'"></p>
                </div>
            </div>
        </div>
    </section>

    {{-- List ujian --}}
    <section class="space-y-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="font-display font-bold text-xl">Daftar Ujian</h2>
                <p class="text-sm text-ink/50 mt-1">Pilih ujian untuk mengetahui kelas yang paling cocok dengan bakatmu.</p>
            </div>
        </div>

        <div x-show="loading" class="text-sm text-ink/40">Memuat daftar ujian...</div>
        <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

        <template x-for="kat in kategori" :key="kat.slug">
            <div :id="'kategori-' + kat.slug" class="scroll-mt-24">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full" :class="'bg-brand-' + kat.warna"></span>
                    <h3 class="font-display font-semibold text-sm uppercase tracking-wide text-ink/60" x-text="kat.nama"></h3>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="ujian in kat.ujian" :key="ujian.id ?? ujian.subcategory">
                        <a :href="(ujian.subcategory ?? '').toLowerCase() === 'portofolio' ? '/portofolio' : '/ujian/' + ujian.id"
                           class="card p-4 flex items-center justify-between hover:shadow-md transition-all group">
                            <div>
                                <p class="font-medium text-sm" x-text="ujian.title ?? ujian.subcategory"></p>
                                <p class="text-xs mt-1 inline-flex px-2 py-0.5 rounded-full"
                                   :class="'tag-' + kat.slug"
                                   x-text="ujian.subcategory ?? ''"></p>
                            </div>
                            <svg class="w-4 h-4 text-ink/30 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </template>
                </div>
            </div>
        </template>
    </section>
</div>
@endsection
