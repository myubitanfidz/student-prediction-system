@extends('layouts.app')
@section('title', $ujian['nama'])

@section('content')
<div x-data="{
        tab: 'pg',
        answered: {{ count(array_filter($pilihanGanda, fn($q) => !is_null($q['jawaban'] ?? null))) }},
        totalPg: {{ count($pilihanGanda) }},
     }"
     class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="tag-{{ $kategoriSlug }} inline-flex px-2.5 py-1 rounded-full text-xs font-medium mb-2">{{ $kategoriNama }}</p>
            <h1 class="font-display font-bold text-2xl">{{ $ujian['nama'] }}</h1>
        </div>
        <div class="text-right shrink-0">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40">Waktu Tersisa</p>
            <p class="font-mono font-semibold text-lg">45:00</p>
        </div>
    </div>

    {{-- Progress --}}
    <div class="card p-4 mb-6">
        <div class="flex justify-between text-xs font-medium text-ink/50 mb-2">
            <span>Progres pengerjaan</span>
            <span x-text="answered + ' / ' + totalPg + ' soal pilihan ganda'"></span>
        </div>
        <div class="h-1.5 rounded-full bg-line overflow-hidden">
            <div class="h-full bg-brand-{{ $kategoriWarna }} transition-all" :style="`width: ${(answered/totalPg)*100}%`"></div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6">
        <button @click="tab = 'pg'"
                :class="tab === 'pg' ? 'bg-ink text-white' : 'bg-white text-ink/50 border border-line'"
                class="px-4 py-2 rounded-full text-sm font-medium transition-colors">
            Pilihan Ganda
        </button>
        <button @click="tab = 'essai'"
                :class="tab === 'essai' ? 'bg-ink text-white' : 'bg-white text-ink/50 border border-line'"
                class="px-4 py-2 rounded-full text-sm font-medium transition-colors">
            Essai
        </button>
    </div>

    <form method="POST" action="{{ route('ujian.submit', $ujian['slug']) }}" class="space-y-4">
        @csrf

        {{-- Pilihan ganda --}}
        <div x-show="tab === 'pg'" class="space-y-4">
            @foreach ($pilihanGanda as $i => $soal)
            <div class="card p-5">
                <p class="text-xs font-semibold text-ink/40 mb-2">Soal {{ $i + 1 }}</p>
                <p class="font-medium mb-4">{{ $soal['pertanyaan'] }}</p>
                <div class="space-y-2">
                    @foreach ($soal['opsi'] as $key => $opsi)
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-line cursor-pointer has-[:checked]:border-brand-{{ $kategoriWarna }} has-[:checked]:bg-brand-{{ $kategoriWarna }}-soft transition-colors">
                        <input type="radio" name="pg[{{ $soal['id'] }}]" value="{{ $key }}"
                               @change="answered = document.querySelectorAll('input[type=radio]:checked').length"
                               class="accent-current text-brand-{{ $kategoriWarna }}"
                               {{ ($soal['jawaban'] ?? null) === $key ? 'checked' : '' }}>
                        <span class="text-sm">{{ $opsi }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Essai --}}
        <div x-show="tab === 'essai'" class="space-y-4">
            @foreach ($essai as $i => $soal)
            <div class="card p-5">
                <p class="text-xs font-semibold text-ink/40 mb-2">Soal Essai {{ $i + 1 }}</p>
                <p class="font-medium mb-3">{{ $soal['pertanyaan'] }}</p>
                <textarea name="essai[{{ $soal['id'] }}]" rows="4" placeholder="Tulis jawabanmu di sini..."
                          class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-{{ $kategoriWarna }}/40 focus:border-brand-{{ $kategoriWarna }}">{{ $soal['jawaban'] ?? '' }}</textarea>
            </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button type="submit" name="aksi" value="simpan" class="btn-ghost">Simpan Sementara</button>
            <button type="submit" name="aksi" value="selesai" class="btn-primary">Selesai &amp; Kumpulkan</button>
        </div>
    </form>
</div>
@endsection
