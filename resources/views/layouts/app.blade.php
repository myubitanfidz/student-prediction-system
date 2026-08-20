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
<body x-data="{ sidebarOpen: false, profileOpen: false }"
      x-init="if (!$store.auth.user) window.location.href = '/login'"
      class="min-h-screen bg-slate-50 text-slate-800">

    <div class="lg:flex">
        {{-- Mobile backdrop --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-ink/25 lg:hidden" aria-hidden="true"></div>

        {{-- Partial Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Main Container --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-screen">
            {{-- Partial Topbar --}}
            @include('layouts.partials.topbar')

            {{-- Main Dynamic Content --}}
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>