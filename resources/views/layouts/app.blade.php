<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') — Talenta Santri</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    <!-- Flatpickr CSS & Theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Modern Select styling */
        select.custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.65rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 2.25rem;
        }

        /* Flatpickr Modern Customization */
        .flatpickr-calendar {
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            border: 1px solid #e2e8f0 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
        }
    </style>
</head>
<body x-data="{ profileOpen: false, pageLoaded: false }"
      x-init="if (!$store.auth.user) window.location.href = '/login'; $nextTick(() => pageLoaded = true)"
      class="min-h-screen bg-[#FFFDF0] text-slate-800 flex flex-col relative">

    @include('layouts.partials.topbar')

    <main class="flex-1 w-full"
          x-show="pageLoaded"
          x-transition:enter="transition-all ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-y-2"
          x-transition:enter-end="opacity-100 translate-y-0">
        @yield('content')
    </main>

    <!-- Global Center Success Modal -->
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

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>