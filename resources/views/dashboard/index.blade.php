@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-5xl mx-auto space-y-10">

    {{-- Nama santri --}}
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-brand-blue text-white text-lg font-display font-bold flex items-center justify-center">
            {{ strtoupper(substr($santri['nama'], 0, 1)) }}
        </div>
        <div>
            <h1 class="font-display font-bold text-2xl">{{ $santri['nama'] }}</h1>
            <p class="text-sm text-ink/50">Rekomendasi kelas berdasarkan hasil ujian di bawah ini.</p>
        </div>
    </div>

    {{-- Rekomendasi kelas --}}
    @if($rekomendasi)
    <div class="card p-6 flex items-center justify-between bg-brand-{{ $rekomendasi['warna'] }}-soft border-brand-{{ $rekomendasi['warna'] }}/30">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-{{ $rekomendasi['warna'] }} mb-1">Kelas Rekomendasi</p>
            <p class="font-display font-bold text-xl">{{ $rekomendasi['nama'] }}</p>
        </div>
        <span class="font-mono font-semibold text-2xl text-brand-{{ $rekomendasi['warna'] }}">{{ $rekomendasi['skor'] }}</span>
    </div>
    @endif

    {{-- Statistik nilai per ujian --}}
    <section>
        <h2 class="font-display font-bold text-xl mb-4">Statistik Nilai</h2>
        <div class="card p-6">
            <div class="grid grid-cols-{{ min(count($statistik), 6) }} gap-6 items-end h-52">
                @foreach ($statistik as $item)
                <div class="flex flex-col items-center gap-2 h-full justify-end">
                    <span class="font-mono text-xs font-semibold">{{ $item['nilai'] }}</span>
                    <div class="w-full rounded-t-md bg-brand-{{ $item['warna'] }}" style="height: {{ $item['nilai'] }}%"></div>
                    <span class="text-[11px] text-ink/50 text-center leading-tight">{{ $item['nama'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Portofolio untuk manual --}}
    <section>
        <h2 class="font-display font-bold text-xl mb-4">Portofolio &mdash; Penilaian Manual</h2>
        <div class="card divide-y divide-line">
            @foreach ($portofolioManual as $item)
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-medium text-sm">{{ $item['nama'] }}</p>
                    <p class="text-xs text-ink/40 mt-0.5">{{ $item['submitted_at'] }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                    {{ $item['status'] === 'Sudah Dinilai' ? 'bg-brand-green-soft text-brand-green' : 'bg-brand-orange-soft text-brand-orange' }}">
                    {{ $item['status'] }}
                </span>
            </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
