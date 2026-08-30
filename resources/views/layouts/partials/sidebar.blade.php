<template x-if="['admin', 'teacher'].includes($store.auth.user?.role)">
    <aside
        class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white border-r border-slate-200 transform transition-transform lg:w-64 lg:max-w-none lg:translate-x-0 lg:static lg:flex lg:flex-col min-h-screen"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="h-16 flex items-center gap-2 px-6 border-b border-slate-200 bg-[#6C5CE7] text-white">
            <div class="bg-white/20 p-1.5 rounded-xl flex items-center gap-1 font-display font-extrabold text-base tracking-tight">
                <span class="text-amber-300">Talent</span>
                <span class="text-white">Mapping</span>
            </div>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1">
            <div class="px-3 pb-2 pt-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span x-text="$store.auth.user?.role === 'teacher' ? 'Menu Guru' : 'Menu Admin'"></span>
            </div>
            
            <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-[#6C5CE7]' : 'text-slate-600 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Daftar Santri & Koreksi
            </a>

            <template x-if="$store.auth.user?.role === 'admin'">
                <a href="{{ route('admin.exams.index') }}" @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.exams.*') ? 'bg-indigo-50 text-[#6C5CE7]' : 'text-slate-600 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Kelola Paket & Soal
                </a>
            </template>
        </nav>

        <div class="p-3 border-t border-slate-200">
            <button @click="$store.auth.logout()" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </div>
    </aside>
</template>