@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="max-w-5xl mx-auto space-y-10">

    {{-- Profil / akun --}}
    <section id="profil" class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
            <div class="w-16 h-16 rounded-full bg-brand-blue text-white text-xl font-display font-bold flex items-center justify-center shrink-0">
                {{ strtoupper(substr($santri['nama'], 0, 1)) }}
            </div>
            <div class="flex-1 grid sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Nama</p>
                    <p class="font-medium">{{ $santri['nama'] }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Email</p>
                    <p class="font-medium">{{ $santri['email'] }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40 mb-1">Kata Sandi</p>
                    <p class="font-medium tracking-widest">••••••••</p>
                </div>
            </div>
            <a href="{{ route('profil.edit') }}" class="btn-ghost shrink-0">Ubah Akun</a>
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

        @foreach ($kategoriUjian as $kategori)
        <div id="kategori-{{ $kategori['slug'] }}" class="scroll-mt-24">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-brand-{{ $kategori['warna'] }}"></span>
                <h3 class="font-display font-semibold text-sm uppercase tracking-wide text-ink/60">{{ $kategori['nama'] }}</h3>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($kategori['ujian'] as $ujian)
                <a href="{{ $ujian['tipe'] === 'portofolio' ? route('portofolio.index', $ujian['slug']) : route('ujian.kerjakan', $ujian['slug']) }}"
                   class="card p-4 flex items-center justify-between hover:border-brand-{{ $kategori['warna'] }}/50 hover:shadow-md transition-all group">
                    <div>
                        <p class="font-medium text-sm">{{ $ujian['nama'] }}</p>
                        <p class="text-xs mt-1 tag-{{ $kategori['slug'] }} inline-flex px-2 py-0.5 rounded-full">
                            {{ $ujian['status'] }}
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-ink/30 group-hover:text-brand-{{ $kategori['warna'] }} group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </section>
</div>
@endsection
