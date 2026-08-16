<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Talenta Santri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cloud flex items-center justify-center p-4">
    <div x-data="registerPage" class="w-full max-w-sm">
        <div class="flex items-center gap-2 justify-center mb-8">
            <span class="w-2.5 h-2.5 rounded-full bg-brand-blue"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-brand-green -ml-1"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-brand-orange -ml-1"></span>
            <span class="font-display font-bold text-lg ml-1">Talenta Santri</span>
        </div>

        <form @submit.prevent="submit" class="card p-6 space-y-4">
            <h1 class="font-display font-bold text-xl mb-1">Buat Akun</h1>
            <p class="text-sm text-ink/50 mb-4">Daftar untuk mulai mengerjakan ujian penempatan.</p>

            <div x-show="error" x-text="error" class="text-sm text-brand-orange bg-brand-orange-soft rounded-lg px-3 py-2"></div>

            <div>
                <label class="text-sm font-medium block mb-1.5">Nama</label>
                <input type="text" x-model="name" required placeholder="Muhammad Santri"
                       class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
            </div>
            <div>
                <label class="text-sm font-medium block mb-1.5">Email</label>
                <input type="email" x-model="email" required placeholder="santri@gmail.com"
                       class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
            </div>
            <div>
                <label class="text-sm font-medium block mb-1.5">Kata Sandi</label>
                <input type="password" x-model="password" required minlength="8" placeholder="Minimal 8 karakter"
                       class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
            </div>

            <button type="submit" :disabled="loading" class="btn-primary w-full">
                <span x-show="!loading">Daftar</span>
                <span x-show="loading">Memproses...</span>
            </button>

            <p class="text-center text-sm text-ink/50">
                Sudah punya akun? <a href="/login" class="text-brand-blue font-medium">Masuk</a>
            </p>
        </form>
    </div>
</body>
</html>
