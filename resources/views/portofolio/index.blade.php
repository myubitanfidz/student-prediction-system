@extends('layouts.app')
@section('title', 'Upload Portofolio — Talent Mapping')

@section('content')
<div x-data="portfolioUploadPage" class="min-h-[calc(100vh-4rem)] bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto bg-[#F8F9FA] rounded-3xl p-6 sm:p-12 border border-slate-100 shadow-sm space-y-8">

        {{-- Header Form --}}
        <div class="space-y-1">
            <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900">
                Upload karya terbaikmu
            </h1>
            <p class="text-xs sm:text-sm text-slate-600">
                Sekarang tunjukkan karya yang paling membuatmu bangga!
            </p>
        </div>

        <form @submit.prevent="submitPortfolio" class="space-y-6">

            {{-- 1. Dropzone Upload Box --}}
            <label class="block w-full border border-slate-900/80 rounded-2xl p-10 sm:p-14 text-center cursor-pointer hover:bg-slate-50 transition">
                <input type="file" multiple class="hidden" @change="handleFileChange($event.target.files)">
                <p class="font-bold text-sm sm:text-base text-slate-900 mb-1">
                    Klik file atau tarik dan taruh disini
                </p>
                <p class="text-xs text-slate-500">
                    JPG, PNG, atau PDF, file size no more than 10Mb
                </p>
            </label>

            {{-- File List Preview --}}
            <template x-if="files.length > 0">
                <div class="space-y-2">
                    <template x-for="(file, idx) in files" :key="idx">
                        <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                            <span class="truncate font-medium text-slate-800" x-text="file.name"></span>
                            <button type="button" @click="removeFile(idx)" class="text-rose-600 font-bold ml-3">✕</button>
                        </div>
                    </template>
                </div>
            </template>

            {{-- 2. Textarea Deskripsi Karya --}}
            <div class="space-y-2">
                <label class="block text-xs sm:text-sm font-semibold text-slate-900">
                    Coba ceritakan sedikit tentang karya mu...
                </label>
                <textarea rows="5" x-model="description" placeholder="Add description"
                          class="w-full bg-[#D9D9D9] border-none rounded-2xl p-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-500 focus:ring-2 focus:ring-slate-400 outline-none"></textarea>
            </div>

            {{-- 3. Jenis Karya Tags --}}
            <div class="space-y-2.5">
                <label class="block text-xs sm:text-sm font-semibold text-slate-900">
                    Jenis Karya
                </label>
                <div class="flex flex-wrap gap-3">
                    <template x-for="type in ['Gambar', 'Coding', 'Video', 'Desain']" :key="type">
                        <button type="button" @click="selectedType = type"
                                class="px-6 py-2.5 rounded-full text-xs sm:text-sm font-semibold transition"
                                :class="selectedType === type ? 'bg-slate-900 text-white shadow-xs' : 'bg-[#D9D9D9] text-slate-800 hover:bg-[#C8C8C8]'">
                            <span x-text="type"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- 4. Submit Button --}}
            <div class="text-center pt-4">
                <button type="submit" :disabled="submitting"
                        class="bg-[#D9D9D9] hover:bg-[#C8C8C8] text-slate-900 font-extrabold text-sm sm:text-base px-12 py-3 rounded-full transition shadow-xs active:scale-95 disabled:opacity-50">
                    <span x-show="!submitting">Unggah!</span>
                    <span x-show="submitting">Mengunggah...</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('portfolioUploadPage', () => ({
        files: [],
        description: '',
        selectedType: 'Desain',
        submitting: false,

        handleFileChange(uploadedFiles) {
            this.files = Array.from(uploadedFiles);
        },
        removeFile(index) {
            this.files.splice(index, 1);
        },

        async submitPortfolio() {
            this.submitting = true;
            const formData = new FormData();
            formData.append('links', `${this.selectedType}: ${this.description}`);
            this.files.forEach(f => formData.append('files[]', f));

            try {
                await fetch('/api/portfolios', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('ts_token')}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                alert('Portofolio berhasil diunggah!');
                window.location.href = '/dashboard';
            } catch (err) {
                console.error(err);
                alert('Gagal mengunggah portofolio');
            } finally {
                this.submitting = false;
            }
        }
    }));
});
</script>
@endsection