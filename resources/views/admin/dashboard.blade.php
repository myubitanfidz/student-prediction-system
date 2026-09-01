@extends('layouts.app')
@section('title', 'Dashboard Admin / Guru')

@section('content')
<div x-data="adminDashboardPage" class="max-w-6xl mx-auto mt-6 sm:mt-8 pb-12 px-4 space-y-6 relative">
    <div x-show="loading" class="text-sm text-ink/40">Memuat daftar santri...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && students.length">
        <div class="space-y-6">
            {{-- Header & Ringkasan --}}
            <div class="flex items-end justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="font-display font-bold text-2xl">Daftar Rekap Santri</h1>
                    <p class="text-sm text-ink/50">Tinjau nilai, portofolio, dan analitik potensi karir berdasarkan gelombang &amp; periode ujian.</p>
                </div>
            </div>

            {{-- Statistik Ringkas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card p-5 border-l-4 border-l-brand-blue space-y-1 shadow-sm">
                    <p class="text-[11px] uppercase tracking-wide text-ink/40 font-semibold">Total Santri Tampil</p>
                    <p class="font-mono font-bold text-2xl" x-text="filteredStudents.length"></p>
                </div>
                <div class="card p-5 border-l-4 border-l-brand-green space-y-1 shadow-sm">
                    <p class="text-[11px] uppercase tracking-wide text-ink/40 font-semibold">Total Test Selesai</p>
                    <p class="font-mono font-bold text-2xl text-brand-green" x-data="animatedCounter(summaryTestsDone)" x-init="start()" x-text="display"></p>
                </div>
                <div class="card p-5 border-l-4 border-l-brand-orange space-y-1 shadow-sm">
                    <p class="text-[11px] uppercase tracking-wide text-ink/40 font-semibold">Rata-rata Highest Score</p>
                    <p class="font-mono font-bold text-2xl text-brand-orange">
                        <span x-data="animatedCounter(summaryAvgHighest)" x-init="start()" x-text="display"></span>%
                    </p>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="card shadow-sm border border-line rounded-2xl">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-4 p-5 border-b border-line bg-slate-50/50 rounded-t-2xl">
                    <div class="relative w-full lg:w-72">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama atau email santri..." 
                               class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-blue outline-none transition-shadow bg-white">
                        <svg class="w-4 h-4 text-ink/40 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                        <!-- Filter Gelombang -->
                        <div class="relative w-full sm:w-auto">
                            <button type="button" @click="periodDropdownOpen = !periodDropdownOpen" 
                                    class="w-full sm:w-auto flex items-center justify-between gap-2.5 px-4 py-2 text-xs font-bold border border-slate-300 rounded-xl bg-white hover:bg-slate-50 transition-colors text-slate-800 shadow-xs">
                                <span class="flex items-center gap-1.5 truncate max-w-[200px]">
                                    <span class="text-slate-400 font-normal">Gelombang:</span>
                                    <span class="text-indigo-600" x-text="filterPeriod || 'Semua Periode'"></span>
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <div x-show="periodDropdownOpen" @click.away="periodDropdownOpen = false" x-cloak 
                                 class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-64 bg-white border border-line rounded-2xl shadow-2xl z-30 py-1.5 overflow-hidden">
                                <button type="button" @click="setPeriod('')" 
                                        class="w-full text-left px-4 py-2.5 text-xs font-semibold hover:bg-slate-50 flex items-center justify-between"
                                        :class="!filterPeriod ? 'text-indigo-600 font-bold bg-indigo-50/60' : 'text-slate-700'">
                                    <span>Semua Periode / Gelombang</span>
                                    <span x-show="!filterPeriod">✓</span>
                                </button>
                                <div class="border-t border-slate-100 my-1"></div>
                                <template x-for="p in availablePeriods" :key="p">
                                    <button type="button" @click="setPeriod(p)" 
                                            class="w-full text-left px-4 py-2.5 text-xs font-semibold hover:bg-slate-50 flex items-center justify-between"
                                            :class="filterPeriod === p ? 'text-indigo-600 font-bold bg-indigo-50/60' : 'text-slate-700'">
                                        <span class="truncate" x-text="p"></span>
                                        <span x-show="filterPeriod === p">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Sort Menu -->
                        <div class="relative w-full sm:w-auto">
                            <button type="button" @click="sortDropdownOpen = !sortDropdownOpen" 
                                    class="w-full sm:w-auto flex items-center justify-between gap-2.5 px-4 py-2 text-xs font-semibold border border-slate-300 rounded-xl bg-white hover:bg-slate-50 transition-colors text-slate-700 shadow-xs">
                                <span x-text="sortBy === 'default' ? 'Urutkan: Default' : (sortBy === 'highest_score' ? 'Skor Tertinggi' : 'Abjad (A-Z)')"></span>
                                <svg class="w-3.5 h-3.5 text-ink/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <div x-show="sortDropdownOpen" @click.away="sortDropdownOpen = false" x-cloak 
                                 class="absolute right-0 mt-2 w-52 bg-white border border-line rounded-2xl shadow-2xl z-30 py-1.5 overflow-hidden">
                                <button type="button" @click="setSort('default')" class="w-full text-left px-4 py-2.5 text-xs hover:bg-slate-50 transition-colors" :class="sortBy === 'default' ? 'text-brand-blue font-bold bg-brand-blue-soft/30' : 'text-slate-700'">Pendaftar Awal (Default)</button>
                                <button type="button" @click="setSort('highest_score')" class="w-full text-left px-4 py-2.5 text-xs hover:bg-slate-50 transition-colors" :class="sortBy === 'highest_score' ? 'text-brand-blue font-bold bg-brand-blue-soft/30' : 'text-slate-700'">Skor Tertinggi ke Terendah</button>
                                <button type="button" @click="setSort('alphabetical')" class="w-full text-left px-4 py-2.5 text-xs hover:bg-slate-50 transition-colors" :class="sortBy === 'alphabetical' ? 'text-brand-blue font-bold bg-brand-blue-soft/30' : 'text-slate-700'">Abjad (A - Z)</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="bg-white border-b border-line text-xs uppercase tracking-wide text-ink/50">
                            <tr>
                                <th class="px-5 py-4 font-semibold text-center w-16">No</th>
                                <th class="px-5 py-4 font-semibold">Nama Santri</th>
                                <th class="px-5 py-4 font-semibold">Periode Ujian Diikuti</th>
                                <th class="px-5 py-4 font-semibold text-center w-32">Test Kelar</th>
                                <th class="px-5 py-4 font-semibold text-center w-32">Porto</th>
                                <th class="px-5 py-4 font-semibold text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line bg-white">
                            <template x-for="(student, index) in paginatedStudents" :key="student.id">
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-4 text-center font-mono text-ink/50" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-900 text-base" x-text="studentName(student)"></p>
                                        <p class="text-xs text-ink/50" x-text="studentEmail(student)"></p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            <template x-for="p in studentPeriods(student)" :key="p">
                                                <span class="inline-block text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200" x-text="p"></span>
                                            </template>
                                            <span x-show="studentPeriods(student).length === 0" class="text-xs text-slate-400 italic">Belum Mengikuti Ujian</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center font-mono font-bold text-brand-blue text-base" x-text="testsDone(student)"></td>
                                    <td class="px-5 py-4 text-center font-mono font-bold text-brand-orange text-base" x-text="portfolioFiles(student).length"></td>
                                    <td class="px-5 py-4 text-center">
                                        <button type="button" @click="activeStudent = student; setTimeout(() => modalAnim = true, 150)" 
                                                class="bg-slate-800 text-white text-xs font-semibold px-5 py-2 rounded-lg hover:bg-slate-700 transition-colors">
                                            Expand
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            
                            <tr x-show="paginatedStudents.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-ink/40">
                                    Tidak ada santri yang cocok dengan filter gelombang / pencarian ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Controls --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-line bg-slate-50/50 rounded-b-2xl">
                    <button type="button" @click="prevPage" :disabled="currentPage === 1" 
                            class="px-4 py-2 text-sm font-medium border border-line rounded-lg bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        ← Sebelumnya
                    </button>
                    <span class="text-sm font-medium text-ink/60" x-text="`Halaman ${currentPage} dari ${totalPages || 1}`"></span>
                    <button type="button" @click="nextPage" :disabled="currentPage === totalPages || totalPages === 0" 
                            class="px-4 py-2 text-sm font-medium border border-line rounded-lg bg-white hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Selanjutnya →
                    </button>
                </div>
            </div>
        </div>
    </template>

    <div x-show="!loading && !error && !students.length" class="card p-8 text-center text-sm text-ink/40 rounded-3xl">
        Belum ada santri terdaftar.
    </div>

    {{-- Detail Modal Popup (Profile & Daftar Ujian yang Diikuti) --}}
    <div x-show="activeStudent" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div @click.away="modalAnim = false; setTimeout(() => activeStudent = null, 300)" 
             x-show="activeStudent" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto flex flex-col relative border border-line">
            
            {{-- Popup Header --}}
            <div class="sticky top-0 bg-white border-b border-line px-6 py-4 flex items-center justify-between z-10 rounded-t-3xl">
                <div>
                    <h2 class="font-display font-bold text-xl text-slate-900" x-text="activeStudent ? studentName(activeStudent) : ''"></h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-xs text-ink/50" x-text="studentEmail(activeStudent)"></p>
                        <span class="text-xs text-slate-300">•</span>
                        <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full"
                              x-text="filterPeriod ? `Gelombang: ${filterPeriod}` : 'Menampilkan Semua Gelombang'"></span>
                    </div>
                </div>
                <button @click="modalAnim = false; setTimeout(() => activeStudent = null, 300)" class="p-2 rounded-full hover:bg-slate-100 text-ink/40 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Popup Content Grid --}}
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Left Column: Analitik Karir --}}
                <div class="md:col-span-1 border border-line rounded-2xl p-5 bg-slate-50 flex flex-col items-center">
                    <h3 class="font-display font-bold text-sm uppercase tracking-wide text-slate-800 w-full text-center mb-1">Analitik Karir</h3>
                    <p class="text-[10px] text-ink/50 text-center mb-6">Prediksi bakat tertinggi</p>
                    
                    <div class="flex items-end justify-between w-full h-36 gap-2 px-1">
                        <template x-if="activeStudent && activeStudent.career_predictions">
                            <template x-for="(score, role) in activeStudent.career_predictions" :key="role">
                                <div class="flex flex-col items-center gap-2 w-full group relative">
                                    <div class="absolute -top-10 bg-slate-800 text-white text-[10px] px-2 py-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10 shadow-lg" x-text="role + ': ' + score + '%'"></div>
                                    <span class="font-mono text-[9px] font-bold text-brand-green" x-text="score + '%'"></span>
                                    
                                    <div class="w-6 h-28 rounded-full border border-slate-200 bg-white overflow-hidden shadow-inner flex flex-col justify-end">
                                        <div class="w-full bg-gradient-to-t from-emerald-500 to-brand-green transition-all duration-1000 ease-out rounded-full"
                                             :style="modalAnim ? `height: ${score}%` : 'height: 0%'"></div>
                                    </div>
                                    
                                    <span class="text-[9px] font-semibold text-ink/60 text-center truncate w-full" x-text="role"></span>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                {{-- Right Column: Portofolio & Daftar Ujian yang Diikuti --}}
                <div class="md:col-span-2 space-y-6">
                    
                    {{-- Portofolio Block --}}
                    <section class="border border-line rounded-2xl p-5 border-l-4 border-l-brand-orange bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-display font-bold text-sm">Portfolio Uploaded</h3>
                            <span class="text-[11px] font-semibold text-ink/40" x-text="activeStudent ? portfolioFiles(activeStudent).length + ' file(s)' : ''"></span>
                        </div>

                        <div x-show="activeStudent?.portfolio?.links" class="text-sm text-ink/60 mb-3 bg-slate-50 p-3 rounded-lg border border-line">
                            <span class="font-semibold text-ink/60 text-xs uppercase block mb-1">Tautan Karya: </span>
                            <a :href="activeStudent?.portfolio?.links" target="_blank" class="text-brand-blue hover:underline break-all" x-text="activeStudent?.portfolio?.links"></a>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <template x-if="activeStudent">
                                <template x-for="file in portfolioFiles(activeStudent)" :key="file">
                                    <span class="px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-xs text-ink/70" x-text="file"></span>
                                </template>
                            </template>
                            <span x-show="activeStudent && !portfolioFiles(activeStudent).length" class="text-sm text-ink/40 italic">Belum ada file portofolio yang diunggah.</span>
                        </div>
                    </section>

                    {{-- Daftar Ujian yang Diikuti --}}
                    <section class="border border-line rounded-2xl p-5 bg-white shadow-sm space-y-3">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-display font-bold text-sm">Daftar Ujian yang Diikuti</h3>
                            <span class="text-[10px] text-slate-400 font-medium" x-text="filterPeriod ? `Gelombang: ${filterPeriod}` : 'Semua Ujian'"></span>
                        </div>

                        <template x-if="activeStudent">
                            <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                                <template x-for="group in revealBars(activeStudent)" :key="group.label">
                                    <div class="space-y-2">
                                        <template x-for="item in group.items" :key="item.exam_id">
                                            <div class="p-4 rounded-xl border border-line bg-slate-50/40 hover:bg-slate-50 transition-colors space-y-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <div class="flex items-center gap-1.5 mb-1">
                                                            <span class="text-[10px] font-extrabold text-brand-blue uppercase px-2 py-0.5 rounded bg-blue-50 border border-blue-200" x-text="group.label"></span>
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200" x-text="item.period_title || 'PSB'"></span>
                                                        </div>
                                                        <!-- Nama Ujian -->
                                                        <h4 class="text-sm font-bold text-slate-900 leading-snug" x-text="item.title"></h4>
                                                    </div>
                                                    
                                                    <div class="text-right shrink-0">
                                                        <span class="font-mono font-black text-lg text-brand-green" x-text="item.value + '%'"></span>
                                                    </div>
                                                </div>

                                                <!-- Action Koreksi Per Ujian -->
                                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 flex-wrap gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[11px] font-medium" :class="item.completed ? 'text-emerald-700' : 'text-slate-400'" x-text="item.completed ? 'Ujian Selesai ✓' : 'Belum Selesai'"></span>
                                                        <button type="button"
                                                                x-show="item.completed"
                                                                @click.stop="allowRetake(activeStudent, item)"
                                                                :disabled="item.retake_allowed || retakeBusy === (activeStudent.id + '-' + item.exam_id)"
                                                                class="text-[10px] font-bold px-2.5 py-0.5 rounded border border-slate-300 hover:bg-white disabled:opacity-50 transition-colors"
                                                                x-text="item.retake_allowed ? 'Izin Aktif' : 'Izinkan Ulang'"></button>
                                                    </div>

                                                    <!-- 🌟 Tombol Buka Koreksi Ujian Spesifik 🌟 -->
                                                    <a :href="`/admin/koreksi/${activeStudent.id}?exam_id=${item.exam_id}`" 
                                                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-white hover:bg-indigo-50 border border-indigo-200 px-3 py-1.5 rounded-lg shadow-2xs transition inline-flex items-center gap-1">
                                                        <span>Koreksi Ujian Ini</span> →
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <div x-show="revealBars(activeStudent).length === 0" class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                    Santri ini belum memiliki riwayat ujian pada gelombang yang dipilih.
                                </div>
                            </div>
                        </template>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection