@extends('layouts.app')
@section('title', 'Total Skor Ujian — Talent Mapping')

@section('content')
<div x-data="hasilDetailPage('{{ $examId }}')" class="min-h-[calc(100vh-4rem)] bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto space-y-8" x-show="!loading" x-cloak>

        {{-- Header Status --}}
        <div class="text-center space-y-2">
            <div class="w-6 h-6 bg-slate-950 rounded-full mx-auto mb-3"></div>
            <h1 class="font-display font-black text-3xl sm:text-4xl text-slate-900">
                Pengerjaan Selesai!
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Berikut adalah perolehan skor akhir untuk ujian kamu
            </p>
        </div>

        {{-- Kartu Total Score Utama --}}
        <div class="bg-[#F8F9FA] rounded-3xl p-8 sm:p-10 border border-slate-100 shadow-sm text-center space-y-6">
            
            {{-- Info Paket Ujian --}}
            <div class="space-y-1.5">
                <span class="inline-block bg-indigo-100 text-indigo-800 text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full" 
                      x-text="examCategory"></span>
                <h2 class="font-display font-black text-2xl text-slate-900" x-text="examTitle"></h2>
            </div>

            {{-- Lingkaran / Kotak Total Score --}}
            <div class="py-2">
                <div class="inline-flex flex-col items-center justify-center w-48 h-48 sm:w-56 sm:h-56 rounded-full bg-white border-4 border-emerald-500/20 shadow-xs mx-auto">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total Score</span>
                    <span class="font-display font-black text-5xl sm:text-6xl text-emerald-600 my-1 font-mono" 
                          x-text="`${totalScore}%`"></span>
                    <span class="text-[11px] font-bold text-slate-500" 
                          x-text="totalScore >= 70 ? 'Hasil Sangat Baik ✓' : 'Perlu Evaluasi'"></span>
                </div>
            </div>

            {{-- Ringkasan Keterangan --}}
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 text-xs text-slate-600 leading-relaxed max-w-md mx-auto">
                Skor ini mencakup akumulasi penilaian otomatis pilihan ganda dan hasil analisis semantik esai.
            </div>

        </div>

        {{-- Tombol Navigasi Bawah --}}
        <div class="text-center space-y-3 pt-2">
            <div>
                <a href="{{ route('portofolio.index') }}"
                   class="inline-block w-full sm:w-auto bg-[#D9D9D9] hover:bg-[#C8C8C8] text-slate-900 font-extrabold text-sm px-8 py-3.5 rounded-2xl transition shadow-xs">
                    Upload Portofolio Sekarang
                </a>
            </div>
            <div>
                <a href="{{ route('profile') }}" class="text-xs sm:text-sm text-slate-700 hover:text-slate-950 font-bold inline-block">
                    ← Kembali ke Daftar Riwayat Ujian
                </a>
            </div>
        </div>

    </div>

    {{-- Loading Spinner --}}
    <div x-show="loading" class="py-24 text-center space-y-3">
        <div class="w-8 h-8 border-4 border-slate-900 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="text-xs font-semibold text-slate-500">Menghitung total skor...</p>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('hasilDetailPage', (examId) => ({
        examId,
        loading: true,
        examTitle: 'Ujian Talent Mapping',
        examCategory: 'Umum',
        totalScore: 0,

        async init() {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            try {
                const res = await fetch('/api/dashboard', {
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Accept': 'application/json' 
                    }
                });
                const json = await res.json();
                const allStats = json?.data?.exam_stats || [];

                // Cari data ujian spesifik berdasarkan ID
                const current = allStats.find(s => String(s.exam_id) === String(this.examId)) || allStats[0];

                if (current) {
                    this.examTitle = current.exam_title || 'Ujian Talent Mapping';
                    this.examCategory = current.category || current.subcategory || 'Umum';
                    this.totalScore = Math.round(current.mc_accuracy_pct || 0);
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection