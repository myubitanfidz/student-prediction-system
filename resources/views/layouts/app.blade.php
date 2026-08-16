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
<body x-data="{ sidebarOpen: false }"
      x-init="if (!$store.auth.user) window.location.href = '/login'"
      class="min-h-screen">

    <div class="lg:flex">

        {{-- Mobile-only backdrop for the off-canvas navigation --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-ink/25 lg:hidden" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 w-72 max-w-[85vw] bg-white border-r border-line transform transition-transform lg:w-64 lg:max-w-none lg:translate-x-0 lg:static lg:flex lg:flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center gap-2 px-6 border-b border-line">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-blue"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-brand-green -ml-1"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-brand-orange -ml-1"></span>
                <span class="font-display font-bold text-[15px] ml-1">Talenta Santri</span>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1">
                <a href="{{ route('beranda') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('beranda') ? 'bg-cloud text-ink' : 'text-ink/60 hover:bg-cloud' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-4h2v4a1 1 0 001 1h4a1 1 0 001-1V10"/></svg>
                    Beranda
                </a>
                <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-cloud text-ink' : 'text-ink/60 hover:bg-cloud' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8 17V10m5 7V6m5 11v-8"/></svg>
                    Dashboard
                </a>

            </nav>

            <div class="p-3 border-t border-line">
                <button @click="$store.auth.logout()" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-ink/50 hover:bg-cloud hover:text-ink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            {{-- Topbar --}}
            <header class="h-16 flex items-center justify-between px-4 lg:px-8 border-b border-line bg-white sticky top-0 z-30">
                <button class="lg:hidden text-ink/60" @click="sidebarOpen = !sidebarOpen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="font-display font-bold text-lg truncate px-3">@yield('title', 'Beranda')</h1>
                <a href="{{ route('beranda') }}#profil" class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-brand-blue text-white text-xs font-semibold flex items-center justify-center"
                          x-text="($store.auth.user?.name ?? 'S').charAt(0).toUpperCase()"></span>
                </a>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
