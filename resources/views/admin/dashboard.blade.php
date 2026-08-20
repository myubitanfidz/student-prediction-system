@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div x-data="adminDashboardPage" class="max-w-5xl mx-auto space-y-6">
    <div x-show="loading" class="text-sm text-ink/40">Memuat daftar santri...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && students.length">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-display font-bold text-2xl">Daftar Santri</h1>
                    <p class="text-sm text-ink/50">Kelola nilai dan koreksi jawaban santri.</p>
                </div>
                <span class="text-xs font-medium bg-cloud rounded-full px-3 py-1" x-text="students.length + ' santri'"></span>
            </div>

            <div class="card overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] font-semibold uppercase tracking-wide text-ink/40 border-b border-line">
                            <th class="px-4 py-3">Santri</th>
                            <th class="px-4 py-3">Total Terjawab</th>
                            <th class="px-4 py-3">Total Nilai</th>
                            <th class="px-4 py-3">Portofolio</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="student in students" :key="student.id">
                            <tr class="border-b border-line last:border-0">
                                <td class="px-4 py-3">
                                    <p class="font-medium" x-text="student.name"></p>
                                    <p class="text-xs text-ink/50" x-text="student.email"></p>
                                </td>
                                <td class="px-4 py-3" x-text="student.total_answered"></td>
                                <td class="px-4 py-3" x-text="student.total_score ?? '-'"></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                                          :class="student.has_portfolio ? 'bg-brand-green-soft text-brand-green' : 'bg-cloud text-ink/40'"
                                          x-text="student.has_portfolio ? 'Ada' : 'Belum'"></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a :href="'/admin/koreksi/' + student.id"
                                       class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-blue hover:underline">
                                        Koreksi
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <div x-show="!loading && !error && !students.length" class="card p-8 text-center text-sm text-ink/40">
        Belum ada santri terdaftar.
    </div>
</div>
@endsection