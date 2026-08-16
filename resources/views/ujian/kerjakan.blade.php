@extends('layouts.app')
@section('title', 'Ujian')

@section('content')
<div x-data="examPage({{ (int) $examId }})" class="max-w-3xl mx-auto">

    <div x-show="loading" class="text-sm text-ink/40">Memuat soal...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && exam">
        <div>
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium mb-2"
                       :class="'tag-' + (exam.category ?? '').toLowerCase()" x-text="exam.category"></p>
                    <h1 class="font-display font-bold text-2xl" x-text="exam.title"></h1>
                </div>
            </div>

            {{-- Progress --}}
            <div class="card p-4 mb-6" x-show="pilihanGanda.length">
                <div class="flex justify-between text-xs font-medium text-ink/50 mb-2">
                    <span>Progres pengerjaan</span>
                    <span x-text="answeredCount + ' / ' + pilihanGanda.length + ' soal pilihan ganda'"></span>
                </div>
                <div class="h-1.5 rounded-full bg-line overflow-hidden">
                    <div class="h-full transition-all" :class="'bg-brand-' + exam.warna" :style="`width: ${(answeredCount/(pilihanGanda.length||1))*100}%`"></div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-2 mb-6">
                <button @click="tab = 'pg'" :class="tab === 'pg' ? 'bg-ink text-white' : 'bg-white text-ink/50 border border-line'"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-colors" x-show="pilihanGanda.length">
                    Pilihan Ganda
                </button>
                <button @click="tab = 'essai'" :class="tab === 'essai' ? 'bg-ink text-white' : 'bg-white text-ink/50 border border-line'"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-colors" x-show="essai.length">
                    Essai
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-4">

                <div x-show="tab === 'pg'" class="space-y-4">
                    <template x-for="(soal, i) in pilihanGanda" :key="soal.id">
                        <div class="card p-5">
                            <p class="text-xs font-semibold text-ink/40 mb-2" x-text="'Soal ' + (i + 1)"></p>
                            <p class="font-medium mb-4" x-text="soal.question_text"></p>
                            <div class="space-y-2">
                                <template x-for="opsi in soal.options" :key="opsi">
                                    <label class="flex items-center gap-3 p-3 rounded-lg border border-line cursor-pointer has-[:checked]:border-brand-blue has-[:checked]:bg-brand-blue-soft transition-colors">
                                        <input type="radio" :name="'pg-' + soal.id" :value="opsi" x-model="jawaban[soal.id]">
                                        <span class="text-sm" x-text="opsi"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="tab === 'essai'" class="space-y-4">
                    <template x-for="(soal, i) in essai" :key="soal.id">
                        <div class="card p-5">
                            <p class="text-xs font-semibold text-ink/40 mb-2" x-text="'Soal Essai ' + (i + 1)"></p>
                            <p class="font-medium mb-3" x-text="soal.question_text"></p>
                            <textarea rows="4" placeholder="Tulis jawabanmu di sini..." x-model="jawaban[soal.id]"
                                      class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue"></textarea>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" :disabled="submitting" class="btn-primary">
                        <span x-show="!submitting">Selesai &amp; Kumpulkan</span>
                        <span x-show="submitting">Mengirim...</span>
                    </button>
                </div>
            </form>
        </div>
    </template>
</div>
@endsection
