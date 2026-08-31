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
<body x-data="{ profileOpen: false }"
      x-init="if (!$store.auth.user) window.location.href = '/login'"
      class="min-h-screen bg-[#FFFDF0] text-slate-800 flex flex-col">

    {{-- Topbar Ungu Terpadu --}}
    @include('layouts.partials.topbar')

    {{-- Main Container Full Width --}}
    <main class="flex-1 w-full">
        @yield('content')
    </main>

</body>
</html>