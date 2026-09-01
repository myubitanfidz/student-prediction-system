<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') — Talenta Santri</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ profileOpen: false, pageLoaded: false }"
      x-init="if (!$store.auth.user) window.location.href = '/login'; $nextTick(() => pageLoaded = true)"
      class="min-h-screen bg-[#FFFDF0] text-slate-800 flex flex-col relative">

    {{-- Topbar Ungu Terpadu --}}
    @include('layouts.partials.topbar')

    {{-- Main Container Full Width --}}
    <main class="flex-1 w-full"
          x-show="pageLoaded"
          x-transition:enter="transition-all ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-y-2"
          x-transition:enter-end="opacity-100 translate-y-0">
        @yield('content')
    </main>

    {{-- 🌟 CENTER SUCCESS MODAL ANIMATION 🌟 --}}
    <div x-data="centerSuccessModal"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-75"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-75"
             class="bg-white rounded-3xl p-8 sm:p-10 max-w-sm w-full text-center space-y-4 shadow-2xl border border-slate-100">
            
            {{-- Animated Circle Checkmark --}}
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto text-emerald-500 shadow-inner">
                <svg class="w-10 h-10 animate-checkmark" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <div class="space-y-1">
                <h3 class="font-display font-extrabold text-xl text-slate-900">Berhasil!</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="message"></p>
            </div>
        </div>
    </div>

    <style>
    @keyframes checkmarkAnim {
        0% { transform: scale(0.4) rotate(-15deg); opacity: 0; }
        60% { transform: scale(1.15) rotate(5deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    .animate-checkmark {
        animation: checkmarkAnim 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    </style>
</body>
</html>