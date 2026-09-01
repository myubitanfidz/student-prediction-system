@extends('layouts.app')
@section('title', 'Koreksi Jawaban & Karya Santri')

@section('content')
<div x-data="adminKoreksiPage" class="max-w-4xl mx-auto mt-6 sm:mt-8 pb-12 px-4 space-y-6">
    <div x-show="loading" class="text-sm text-ink/40">Memuat jawaban santri...</div>
    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

    <template x-if="!loading && student">
        <div class="space-y-6">
            {{-- Header Santri --}}
            <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-line shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-brand-blue text-white text-base font-display font-bold flex items-center justify-center shadow-xs"
                         x-text="student.name.charAt(0).toUpperCase()"></div>
                    <div>
                        <h1 class="font-display font-bold text-xl text-slate-900" x-text="student.name"></h1>
                        <p class="text-xs text-slate-500" x-text="student.email"></p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-4 py-2 rounded-xl transition">
                    ← Kembali ke Dashboard
                </a>
            </div>

            <template x-if="answers.length">
                <div class="space-y-4">
                    <template x-for="answer in answers" :key="answer.answer_id">
                        <article class="card p-6 space-y-4 bg-white rounded-2xl border border-line shadow-xs">
                            {{-- Header Pertanyaan --}}
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600" x-text="answer.exam_title"></p>
                                    <h3 class="font-display font-bold text-base text-slate-900" x-text="answer.question_text"></h3>
                                    
                                    {{-- Badge Tipe Soal --}}
                                    <div class="flex items-center gap-2 pt-1">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                              :class="{
                                                  'bg-blue-100 text-blue-800': answer.question_type === 'multiple_choice',
                                                  'bg-purple-100 text-purple-800': answer.question_type === 'essay',
                                                  'bg-emerald-100 text-emerald-800': answer.question_type === 'image_upload'
                                              }"
                                              x-text="answer.question_type === 'multiple_choice' ? 'Pilihan Ganda' : (answer.question_type === 'image_upload' ? 'Upload Karya Gambar' : 'Esai')"></span>
                                        
                                        <template x-if="answer.gclwama_tag">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700"
                                                  x-text="`Bagian ${answer.gclwama_tag}`"></span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Status Nilai Saat Ini --}}
                                <div class="text-right shrink-0">
                                    <template x-if="answer.current_score != null">
                                        <div>
                                            <p class="font-mono text-xl font-black text-emerald-600" x-text="answer.current_score + '%'"></p>
                                            <p class="text-[10px] uppercase font-bold text-slate-400">Telah Dinilai</p>
                                        </div>
                                    </template>
                                    <template x-if="answer.current_score == null">
                                        <span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                            Belum Dinilai
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- KONTEN 1: PENAMPIL GAMBAR KARYA (Tipe image_upload) --}}
                            <template x-if="answer.question_type === 'image_upload'">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                                    <p class="text-xs font-bold text-slate-700">Hasil Karya Gambar / Foto yang Diunggah:</p>
                                    <template x-if="answer.file_url">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                            <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-300 bg-white"
                                                 @click="previewModalImg = answer.file_url">
                                                <img :src="answer.file_url" 
                                                     class="h-44 w-auto max-w-full object-contain rounded-xl hover:scale-105 transition duration-300">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">
                                                    🔍 Klik untuk Memperbesar
                                                </div>
                                            </div>
                                            <div class="space-y-1 text-xs">
                                                <a :href="answer.file_url" target="_blank" class="text-indigo-600 font-bold hover:underline flex items-center gap-1">
                                                    <span>Buka di tab baru</span> ↗
                                                </a>
                                                <p class="text-slate-400 text-[11px]">Format gambar terverifikasi.</p>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!answer.file_url">
                                        <p class="text-xs text-slate-400 italic">Santri tidak melampirkan berkas foto.</p>
                                    </template>
                                </div>
                            </template>

                            {{-- KONTEN 2: JAWABAN TEKS / ESAI --}}
                            <template x-if="answer.question_type !== 'image_upload'">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">Jawaban Santri:</p>
                                    <p class="text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-wrap" x-text="answer.student_answer || '— (Kosong)'"></p>
                                </div>
                            </template>

                            {{-- FORM INPUT NILAI LANGSUNG (Khusus Essay & Image Upload) --}}
                            <template x-if="['essay', 'image_upload'].includes(answer.question_type)">
                                <div class="flex items-center justify-between flex-wrap gap-3 pt-2 border-t border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-bold uppercase text-slate-600">Beri Nilai (0–100):</label>
                                        <input type="number" min="0" max="100" x-model="scores[answer.answer_id]"
                                               placeholder="0-100"
                                               class="w-28 rounded-xl border border-line p-2 text-sm font-mono font-bold text-center focus:outline-none focus:ring-2 focus:ring-brand-blue/40 bg-white">
                                    </div>
                                    <button type="button" @click="saveScore(answer.answer_id)"
                                            class="btn-primary text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs active:scale-95 transition">
                                        Simpan Nilai
                                    </button>
                                </div>
                            </template>
                        </article>
                    </template>
                </div>
            </template>

            <div x-show="!answers.length" class="card p-8 text-center text-sm text-ink/40">
                Santri ini belum mengerjakan ujian apapun.
            </div>
        </div>
    </template>

    {{-- Lightbox Modal Full-Size Image Preview --}}
    <div x-show="previewModalImg" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs"
         @click="previewModalImg = null">
        <div class="relative max-w-4xl max-h-[90vh] bg-white p-2 rounded-2xl shadow-2xl overflow-hidden" @click.stop>
            <button @click="previewModalImg = null" class="absolute top-4 right-4 bg-slate-900/70 hover:bg-slate-900 text-white rounded-full p-2 text-xs font-bold">
                ✕
            </button>
            <img :src="previewModalImg" class="max-h-[80vh] w-auto mx-auto object-contain rounded-xl">
        </div>
    </div>
</div>
@endsection