<header class="h-16 flex items-center justify-between px-4 lg:px-8 border-b border-line bg-white sticky top-0 z-30">
    <div class="flex items-center gap-3">
        <button class="lg:hidden text-ink/60 p-1" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="font-display font-bold text-lg truncate">@yield('title', 'Beranda')</h1>
        <template x-if="['admin', 'teacher'].includes($store.auth.user?.role)">
            <span class="text-xs font-semibold px-2 py-0.5 rounded"
                  :class="$store.auth.user?.role === 'teacher' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700'"
                  x-text="$store.auth.user?.role === 'teacher' ? 'Guru' : 'Admin'"></span>
        </template>
    </div>

    <div class="relative">
        <button type="button" @click="profileOpen = !profileOpen" class="flex items-center gap-2" aria-label="Buka menu profil">
            <span class="w-8 h-8 rounded-full bg-brand-blue text-white text-xs font-semibold flex items-center justify-center"
                  x-text="($store.auth.user?.name ?? 'S').charAt(0).toUpperCase()"></span>
        </button>
        <div x-show="profileOpen" x-cloak x-transition @click.outside="profileOpen = false"
             class="absolute right-0 top-11 z-40 w-40 rounded-xl border border-line bg-white p-1.5 shadow-lg">
            <a href="{{ route('profile') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink/70 hover:bg-cloud">Profil Saya</a>
            <button type="button" @click="$store.auth.logout()" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-ink/70 hover:bg-cloud">Keluar</button>
        </div>
    </div>
</header>