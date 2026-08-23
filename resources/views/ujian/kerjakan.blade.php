@extends('layouts.app')
@section('title', 'Ujian')

@section('content')
<div x-data="examPage({{ (int) $examId }})" class="max-w-3xl mx-auto">

    <div x-show="loading" class="text-sm text-ink/40">Memuat soal...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2 mb-4"></div>

    <template x-if="!loading && exam">
        <div>
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <p class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium mb-2"
                       :class="'tag-' + (exam.category ?? '').toLowerCase()" x-text="exam.category"></p>
                    <h1 class="font-display font-bold text-2xl" x-text="exam.title"></h1>
                    <p x-show="completed" class="text-sm text-ink/50 mt-1">Hasil ujian — Anda tidak dapat mengerjakan ulang tanpa izin guru.</p>
                    <p x-show="retakeAllowed" class="text-sm text-brand-green mt-1">Izin ulang aktif — kerjakan kembali dari awal.</p>
                </div>
                <a href="{{ route('beranda') }}" class="text-sm font-medium text-brand-blue hover:underline shrink-0">← Beranda</a>
            </div>

            {{-- ========== RESULTS MODE ========== --}}
            <template x-if="completed">
                <div class="space-y-4">
                    <div class="card p-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-ink/40">Ringkasan</p>
                            <p class="font-display font-bold text-lg mt-0.5">
                                <span x-text="correctCount"></span> benar
                                <span class="text-ink/30 font-normal">/</span>
                                <span x-text="mcTotal"></span> PG
                            </p>
                        </div>
                        <span class="font-mono font-bold text-2xl text-brand-green" x-text="mcAccuracy + '%'"></span>
                    </div>

                    <template x-for="(item, i) in results" :key="item.id">
                        <article class="card p-5 space-y-3"
                                 :class="item.type === 'multiple_choice'
                                    ? (item.is_correct ? 'border-l-4 border-l-brand-green' : 'border-l-4 border-l-rose-400')
                                    : 'border-l-4 border-l-brand-orange'">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-ink/40 mb-1" x-text="'Soal ' + (i + 1)"></p>
                                    <p class="font-medium" x-text="item.question_text"></p>
                                </div>
                                <template x-if="item.type === 'multiple_choice'">
                                    <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full"
                                          :class="item.is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                          x-text="item.is_correct ? 'Benar' : 'Salah'"></span>
                                </template>
                                <template x-if="item.type === 'essay'">
                                    <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">Esai</span>
                                </template>
                            </div>

                            <div class="text-sm space-y-1">
                                <p class="text-ink/60">
                                    <span class="font-medium text-ink/80">Jawaban Anda:</span>
                                    <span x-text="item.student_answer || '—'"></span>
                                </p>
                                <template x-if="item.type === 'multiple_choice' && !item.is_correct">
                                    <p class="text-brand-green">
                                        <span class="font-medium">Kunci:</span>
                                        <span x-text="item.correct_answer"></span>
                                    </p>
                                </template>
                                <template x-if="item.type === 'essay'">
                                    <p class="text-ink/50">
                                        Nilai esai:
                                        <span class="font-mono font-semibold text-ink"
                                              x-text="item.score != null ? item.score + '%' : 'Belum dikoreksi'"></span>
                                    </p>
                                </template>
                            </div>
                        </article>
                    </template>
                </div>
            </template>

            {{-- ========== TAKING MODE — one question at a time ========== --}}
            <template x-if="!completed && questions.length">
                <div>
                    <div class="card p-4 mb-6">
                        <div class="flex justify-between text-xs font-medium text-ink/50 mb-2">
                            <span>Progres pengerjaan</span>
                            <span x-text="(currentIndex + 1) + ' / ' + questions.length + ' soal'"></span>
                        </div>
                        <div class="h-1.5 rounded-full bg-line overflow-hidden">
                            <div class="h-full transition-all duration-300"
                                 :class="'bg-brand-' + exam.warna"
                                 :style="`width: ${((currentIndex + 1) / questions.length) * 100}%`"></div>
                        </div>
                    </div>

                    <div class="card p-5 sm:p-6 space-y-4"
                         x-show="currentQuestion"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-2"
                         x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold text-ink/40" x-text="'Soal ' + (currentIndex + 1)"></p>
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full"
                                  :class="currentQuestion?.type === 'multiple_choice' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'"
                                  x-text="currentQuestion?.type === 'multiple_choice' ? 'Pilihan Ganda' : 'Esai'"></span>
                        </div>

                        <p class="font-medium text-base sm:text-lg" x-text="currentQuestion?.question_text"></p>

                        {{-- Multiple choice --}}
                        <div class="space-y-2" x-show="currentQuestion?.type === 'multiple_choice'">
                            <template x-for="opsi in (currentQuestion?.options || [])" :key="opsi">
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-line cursor-pointer has-[:checked]:border-brand-blue has-[:checked]:bg-brand-blue-soft transition-colors">
                                    <input type="radio"
                                           :name="'q-' + currentQuestion.id"
                                           :value="opsi"
                                           x-model="jawaban[currentQuestion.id]">
                                    <span class="text-sm" x-text="opsi"></span>
                                </label>
                            </template>
                        </div>

                        {{-- Essay --}}
                        <div x-show="currentQuestion?.type === 'essay'">
                            <textarea rows="5"
                                      placeholder="Tulis jawabanmu di sini..."
                                      x-model="jawaban[currentQuestion.id]"
                                      class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-6">
                        <button type="button" @click="prev()"
                                :disabled="currentIndex === 0"
                                class="px-4 py-2 rounded-lg border border-line text-sm font-medium text-ink/70 hover:bg-cloud disabled:opacity-40 disabled:cursor-not-allowed">
                            Sebelumnya
                        </button>

                        <div class="flex gap-3">
                            <button type="button" x-show="!isLast" @click="next()"
                                    class="btn-primary text-sm px-5 py-2 w-full sm:w-auto">
                                Selanjutnya
                            </button>
                            <button type="button" x-show="isLast" @click="submit()" :disabled="submitting"
                                    class="btn-primary text-sm px-5 py-2 w-full sm:w-auto">
                                <span x-show="!submitting">Selesai &amp; Kumpulkan</span>
                                <span x-show="submitting">Mengirim...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="!completed && !questions.length && !loading" class="card p-8 text-center text-sm text-ink/40">
                Belum ada soal pada ujian ini.
            </div>
        </div>
    </template>
</div>
@endsection
