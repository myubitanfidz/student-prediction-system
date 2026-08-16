@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div x-data="berandaPage" class="max-w-5xl mx-auto space-y-10">

    <section class="card p-6">
        <p class="text-sm text-ink/50">Hello!</p>
        <h2 class="font-display font-bold text-2xl mt-1">Welcome, <span x-text="user?.name ?? 'Santri'"></span></h2>
        <p class="text-sm text-ink/50 mt-2">Pilih kategori lalu mulai tes untuk melihat kecocokanmu.</p>
    </section>

    <section class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
            <div>
                <h2 class="font-display font-bold text-xl">Daftar Ujian</h2>
                <p class="text-sm text-ink/50 mt-1">Pilih ujian untuk mengetahui kelas yang paling cocok dengan bakatmu.</p>
            </div>
        </div>

        <div x-show="loading" class="text-sm text-ink/40">Memuat daftar ujian...</div>
        <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

        <template x-for="kat in kategori" :key="kat.slug">
            <div :id="'kategori-' + kat.slug" class="card scroll-mt-24 overflow-hidden transition-shadow"
                 :class="isCategoryComplete(kat) ? 'ring-2 ring-brand-green border-brand-green' : ''">
                <button type="button"
                        @click="openCategory = openCategory === kat.slug ? null : kat.slug"
                        :aria-expanded="openCategory === kat.slug"
                        class="w-full flex items-center justify-between gap-4 p-5 text-left hover:bg-cloud transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full" :class="'bg-brand-' + kat.warna"></span>
                        <span>
                            <span class="block font-display font-semibold text-base" x-text="kat.nama"></span>
                            <span class="block text-sm text-ink/50 mt-0.5" x-text="kat.ujian.length + ' pilihan tes'"></span>
                        </span>
                    </span>
                    <span class="flex items-center gap-2">
                        <svg x-show="isCategoryComplete(kat)" class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                        <svg class="w-5 h-5 shrink-0 text-ink/40 transition-transform" :class="openCategory === kat.slug && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </button>
                <div x-show="openCategory === kat.slug" x-transition x-cloak class="border-t border-line p-4 sm:p-5">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="ujian in kat.ujian" :key="ujian.id ?? ujian.subcategory">
                            <a :href="(ujian.subcategory ?? '').toLowerCase() === 'portofolio' ? '/portofolio' : '/ujian/' + ujian.id"
                               class="card p-4 flex items-center justify-between hover:shadow-md transition-all group"
                               :class="ujian.completed ? 'ring-1 ring-brand-green border-brand-green' : ''">
                                <div>
                                    <p class="font-medium text-sm" x-text="ujian.title ?? ujian.subcategory"></p>
                                    <p class="text-xs mt-1 inline-flex px-2 py-0.5 rounded-full"
                                       :class="'tag-' + kat.slug"
                                       x-text="ujian.subcategory ?? ''"></p>
                                </div>
                                <svg x-show="ujian.completed" class="w-5 h-5 shrink-0 text-brand-green" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                                <svg x-show="!ujian.completed" class="w-4 h-4 text-ink/30 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        {{-- Portfolio is an action card, so it opens a modal instead of a test dropdown. --}}
        <div x-data="portofolioPage">
            <button type="button" @click="open = true" class="card w-full p-5 flex items-center justify-between gap-4 text-left hover:shadow-md transition-all">
                <span class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-green"></span>
                    <span>
                        <span class="block font-display font-semibold text-base">Portofolio</span>
                        <span class="block text-sm text-ink/50 mt-0.5">Unggah karya atau bagikan tautan terbaikmu.</span>
                    </span>
                </span>
                <svg class="w-5 h-5 text-ink/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
            </button>

            <div x-show="open" x-cloak x-transition.opacity @keydown.escape.window="open = false"
                 class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
                <div @click="open = false" class="absolute inset-0 bg-ink/40"></div>
                <div role="dialog" aria-modal="true" class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-t-xl sm:rounded-xl bg-cloud p-4 sm:p-6">
                    <div class="card p-5 sm:p-6 space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div><p class="tag-it inline-flex px-2.5 py-1 rounded-full text-xs font-medium">IT</p><h3 class="font-display font-bold text-xl mt-2">Upload Portofolio</h3><p class="text-sm text-ink/50 mt-1">Maksimal lima berkas, 100 MB per berkas.</p></div>
                            <button type="button" @click="open = false" class="text-ink/40 hover:text-ink" aria-label="Tutup">×</button>
                        </div>
                        <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div><label class="text-sm font-medium block mb-1.5">GitHub (opsional)</label><input type="url" x-model="linkGithub" placeholder="https://github.com/username/project" class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/40"></div>
                            <div><label class="text-sm font-medium block mb-1.5">YouTube / website (opsional)</label><input type="url" x-model="linkYoutube" placeholder="https://..." class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/40"></div>
                            <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-line rounded-xl2 py-7 cursor-pointer hover:border-brand-green/50 transition-colors" :class="files.length >= maxFiles && 'opacity-40 pointer-events-none'">
                                <svg class="w-6 h-6 text-ink/30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                                <span class="text-sm text-ink/50">Pilih berkas untuk diunggah</span>
                                <input type="file" class="hidden" multiple @change="addFiles($event.target.files); $event.target.value = null">
                            </label>
                            <ul x-show="files.length" class="space-y-1.5"><template x-for="(file, index) in files" :key="index"><li class="flex items-center justify-between gap-3 rounded-lg bg-brand-green-soft px-3 py-2 text-sm"><span class="truncate" x-text="file.name"></span><button type="button" @click="removeFile(index)" class="shrink-0 text-ink/40 hover:text-brand-orange">Hapus</button></li></template></ul>
                            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-1"><button type="button" @click="open = false" class="btn-ghost w-full sm:w-auto">Batal</button><button type="submit" :disabled="submitting" class="btn-primary w-full sm:w-auto"><span x-show="!submitting">Kirim Portofolio</span><span x-show="submitting">Mengunggah...</span></button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
