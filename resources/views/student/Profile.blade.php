@extends('layouts.app')
@section('title', 'Profil & Riwayat Ujian — Talent Mapping')

@section('content')
<div x-data="profileExamsPage" class="min-h-[calc(100vh-4rem)] bg-white py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8" x-cloak>

        {{-- Header Profil Santri --}}
        <div class="bg-[#F8F9FA] rounded-3xl p-6 sm:p-8 border border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xs">
            <div class="flex items-center gap-4 text-center sm:text-left flex-col sm:flex-row">
                <div class="w-16 h-16 rounded-full bg-slate-900 text-white font-black text-2xl flex items-center justify-center shadow-sm">
                    <span x-text="userName ? userName.charAt(0).toUpperCase() : 'U'"></span>
                </div>
                <div>
                    <h1 class="font-display font-black text-2xl text-slate-900" x-text="userName || 'Memuat profil...'"></h1>
                    <p class="text-xs text-slate-500" x-text="userEmail"></p>
                    <span class="inline-block mt-2 bg-indigo-50 text-indigo-700 text-[11px] font-bold px-3 py-0.5 rounded-full border border-indigo-200">Santri Talent Mapping</span>
                </div>
            </div>

            <a href="{{ route('portofolio.index') }}" class="bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-xs shrink-0">
                Portofolio Saya →
            </a>
        </div>

        {{-- Daftar Ujian yang Selesai Dikerjakan --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-display font-extrabold text-xl text-slate-900">Riwayat Hasil Ujian</h2>
                <span class="text-xs text-slate-400" x-text="`${completedExams.length} Ujian Selesai`"></span>
            </div>

            <div x-show="loading" class="py-12 text-center text-slate-400 text-sm">
                Memuat riwayat ujian santri...
            </div>

            <div x-show="!loading && completedExams.length === 0" class="py-12 text-center text-slate-400 text-sm bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                Kamu belum menyelesaikan ujian apa pun. Silakan ikuti ujian di beranda terlebih dahulu.
            </div>

            <div class="grid grid-cols-1 gap-4" x-show="!loading && completedExams.length > 0">
                <template x-for="item in completedExams" :key="item.exam_id">
                    <div class="bg-[#F8F9FA] hover:bg-slate-50 border border-slate-200/80 rounded-2xl p-6 transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-xs">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md"
                                      :class="item.category === 'IT' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800'"
                                      x-text="item.category"></span>
                                <span class="text-xs font-bold text-slate-400" x-text="item.subcategory || 'Umum'"></span>
                            </div>
                            <h3 class="font-display font-black text-lg text-slate-900" x-text="item.exam_title"></h3>
                            <p class="text-xs text-slate-500">Status: <strong class="text-emerald-700">Telah Selesai Dikoreksi ✓</strong></p>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-6 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-200">
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Skor Akhir</span>
                                <span class="font-display font-black text-2xl text-emerald-600" x-text="`${item.final_score}%`"></span>
                            </div>
                            {{-- Mengarahkan ke rute hasil dinamis per exam --}}
                            <a :href="`/hasil/${item.exam_id}`" 
                               class="bg-[#E5E7EB] hover:bg-[#D1D5DB] text-slate-900 font-extrabold text-xs sm:text-sm px-6 py-2.5 rounded-xl transition shadow-xs">
                                Buka Hasil →
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('profileExamsPage', () => ({
        loading: true,
        userName: '',
        userEmail: '',
        completedExams: [],

        async init() {
            const token = localStorage.getItem('ts_token') || localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            try {
                const res = await fetch('/api/dashboard', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                
                this.userName = json?.data?.user?.name || '';
                this.userEmail = json?.data?.user?.email || '';
                const rawStats = json?.data?.exam_stats || [];

                this.completedExams = rawStats.map(stat => ({
                    exam_id: stat.exam_id,
                    exam_title: stat.exam_title || 'Ujian Talent Mapping',
                    category: stat.category || (stat.subcategory === 'GCLWAMA' ? 'IT' : 'Bahasa'),
                    subcategory: stat.subcategory || 'Umum',
                    final_score: Math.round(stat.mc_accuracy_pct || 0)
                }));
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