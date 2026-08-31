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
      class="min-h-screen bg-[#FFFDF0] text-slate-800 flex flex-col">

    {{-- Topbar Ungu Terpadu --}}
    @include('layouts.partials.topbar')

    {{-- Main Container Full Width dengan Animasi Transisi Halaman --}}
    <main class="flex-1 w-full"
          x-show="pageLoaded"
          x-transition:enter="transition-all ease-out duration-500"
          x-transition:enter-start="opacity-0 translate-y-3"
          x-transition:enter-end="opacity-100 translate-y-0">
        @yield('content')
    </main>

</body>
</html>