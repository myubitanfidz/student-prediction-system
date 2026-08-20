@extends('layouts.app')
@section('title', 'Koreksi Jawaban')

@section('content')
<div x-data="adminKoreksiPage" class="max-w-4xl mx-auto space-y-6">
    <div x-show="loading" class="text-sm text-ink/40">Memuat jawaban santri...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && student">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-brand-blue text-white text-base font-display font-bold flex items-center justify-center"
                         x-text="student.name.charAt(0).toUpperCase()"></div>
                    <div>
                        <h1 class="font-display font-bold text-xl" x-text="student.name"></h1>
                        <p class="text-sm text-ink/50" x-text="student.email"></p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-brand-blue hover:underline">← Kembali</a>
            </div>

            <template x-if="answers.length">
                <div class="space-y-4">
                    <template x-for="answer in answers" :key="answer.answer_id">
                        <article class="card p-5 space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1.5 min-w-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ink/40" x-text="answer.exam_title"></p>
                                    <h3 class="font-display font-semibold text-sm" x-text="answer.question_text"></h3>
                                    <p class="text-sm text-ink/70">
                                        <span class="font-medium">Jawaban santri:</span>
                                        <span x-text="answer.student_answer"></span>
                                    </p>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                                          :class="answer.question_type === 'multiple_choice' ? 'bg-brand-blue-soft text-brand-blue' : 'bg-brand-green-soft text-brand-green'"
                                          x-text="answer.question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Esai'"></span>
                                </div>
                                <div x-show="answer.current_score != null" class="text-right shrink-0">
                                    <p class="font-mono text-lg font-semibold" x-text="answer.current_score + '%'"></p>
                                    <p class="text-[11px] text-ink/40">Nilai saat ini</p>
                                </div>
                            </div>
                            <div x-show="answer.question_type === 'essay'" class="flex items-center gap-2">
                                <input type="number" min="0" max="100" x-model="scores[answer.answer_id]"
                                       placeholder="Nilai 0-100"
                                       class="w-32 rounded-lg border border-line p-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
                                <button type="button" @click="saveScore(answer.answer_id)"
                                        class="btn-primary text-sm px-4 py-2">
                                    Simpan Nilai
                                </button>
                            </div>
                        </article>
                    </template>
                </div>
            </template>

            <div x-show="!answers.length" class="card p-8 text-center text-sm text-ink/40">
                Santri ini belum mengerjakan ujian apapun.
            </div>
        </div>
    </template>
</div>
@endsection