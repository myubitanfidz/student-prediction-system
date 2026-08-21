@extends('layouts.app')
@section('title', 'Portofolio')

@section('content')
<div x-data="portofolioPage" class="max-w-2xl mx-auto space-y-6">

    <div>
        <p class="tag-it inline-flex px-2.5 py-1 rounded-full text-xs font-medium mb-2">IT</p>
        <h1 class="font-display font-bold text-2xl">Portofolio</h1>
        <p class="text-sm text-ink/50 mt-1">Bagikan karya terbaikmu — link project dan berkas pendukung dinilai langsung oleh pembimbing.</p>
    </div>

    <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>
    <div x-show="success" class="text-sm text-brand-green bg-brand-green-soft rounded-lg px-3 py-2">Portofolio berhasil dikirim.</div>

    <form @submit.prevent="submit" class="space-y-6">

        {{-- Dynamic Link Portofolio --}}
        <div class="card p-6 space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-ink/40">Link Portofolio</p>
                <p class="text-xs text-ink/45 mt-0.5">Tautkan link karya online seperti repositori GitHub, video YouTube, Behance, Figma, atau website portofolio pribadimu.</p>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in links" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="url" x-model="links[index]" placeholder="https://github.com/... atau https://youtube.com/..."
                               class="flex-1 rounded-lg border border-line p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/40 focus:border-brand-green">
                        <button type="button" @click="removeLink(index)" x-show="links.length > 1"
                                class="p-2 text-ink/40 hover:text-brand-orange transition-colors" title="Hapus Link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Tombol Tambah Link di Bawah Kanan --}}
            <div class="flex justify-end pt-1">
                <button type="button" @click="addLink()"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-green hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Link Portofolio
                </button>
            </div>
        </div>

        {{-- Upload berkas --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-ink/40">Berkas Pendukung</p>
                <span class="text-xs font-mono" :class="files.length >= maxFiles ? 'text-brand-orange' : 'text-ink/40'"
                      x-text="files.length + ' / ' + maxFiles"></span>
            </div>

            <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-line rounded-xl2 py-8 cursor-pointer hover:border-brand-green/50 transition-colors"
                   :class="files.length >= maxFiles && 'opacity-40 pointer-events-none'">
                <svg class="w-6 h-6 text-ink/30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                <span class="text-sm text-ink/50">Klik untuk unggah, maks. 5 berkas &middot; 100MB/berkas</span>
                <input type="file" class="hidden" multiple
                       @change="addFiles($event.target.files); $event.target.value = null">
            </label>

            <ul class="space-y-2" x-show="files.length > 0">
                <template x-for="(file, index) in files" :key="index">
                    <li class="flex items-center justify-between px-3 py-2 rounded-lg bg-brand-green-soft text-sm">
                        <span class="truncate" x-text="file.name"></span>
                        <button type="button" @click="removeFile(index)" class="text-ink/40 hover:text-brand-orange shrink-0 ml-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </li>
                </template>
            </ul>
        </div>

        <div class="flex justify-end">
            <button type="submit" :disabled="submitting" class="btn-primary w-full sm:w-auto">
                <span x-show="!submitting">Kirim Portofolio</span>
                <span x-show="submitting">Mengirim...</span>
            </button>
        </div>
    </form>
</div>
@endsection