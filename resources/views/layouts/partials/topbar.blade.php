<header class="w-full bg-[#6C5CE7] text-white px-4 sm:px-6 lg:px-12 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
    <div class="flex items-center gap-6">
        {{-- Logo Brand --}}
        <a href="{{ route('beranda') }}" class="bg-white/20 p-1.5 rounded-xl flex items-center gap-1 font-display font-extrabold text-base sm:text-lg tracking-tight hover:bg-white/25 transition">
            <span class="text-amber-300">Talent</span>
            <span class="text-white">Mapping</span>
        </a>

        {{-- Badge Role Admin/Guru --}}
        <template x-if="['admin', 'teacher'].includes($store.auth.user?.role)">
            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-white/20 text-white"
                  x-text="$store.auth.user?.role === 'teacher' ? 'Guru' : 'Admin'"></span>
        </template>

        {{-- Navigasi Khusus Admin & Guru (Dipindahkan dari Sidebar) --}}
        <template x-if="['admin', 'teacher'].includes($store.auth.user?.role)">
            <nav class="hidden md:flex items-center gap-4 text-sm font-semibold">
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.koreksi') ? 'bg-white/20 text-amber-300' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                    Daftar Santri &amp; Koreksi
                </a>
                <template x-if="$store.auth.user?.role === 'admin'">
                    <a href="{{ route('admin.exams.index') }}" 
                       class="px-3 py-1.5 rounded-lg transition-colors {{ request()->routeIs('admin.exams.*') ? 'bg-white/20 text-amber-300' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        Kelola Paket &amp; Soal
                    </a>
                </template>
            </nav>
        </template>
    </div>

    {{-- Navigasi Desktop Santri --}}
    <template x-if="!['admin', 'teacher'].includes($store.auth.user?.role)">
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
            <a href="{{ route('beranda') }}" class="text-white hover:text-amber-300 transition-colors {{ request()->routeIs('beranda') ? 'text-amber-300' : '' }}">Home</a>
            <a href="{{ route('beranda') }}#bidang" class="text-white/80 hover:text-white transition-colors">Bidang</a>
            <a href="{{ route('beranda') }}#about" class="text-white/80 hover:text-white transition-colors">About Us</a>
        </nav>
    </template>

    {{-- Menu Profile / Dropdown --}}
    <div class="relative">
        <button type="button" @click="profileOpen = !profileOpen" class="w-9 h-9 rounded-lg bg-white/20 border border-white/30 flex items-center justify-center font-bold text-sm text-white hover:bg-white/30 transition" aria-label="Buka menu profil">
            <span x-text="($store.auth.user?.name ?? 'S').charAt(0).toUpperCase()"></span>
        </button>

        <div x-show="profileOpen" x-cloak x-transition @click.outside="profileOpen = false"
             class="absolute right-0 top-11 z-50 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl text-slate-700">
            <div class="px-3 py-2 border-b border-slate-100">
                <p class="text-xs font-bold text-slate-900 truncate" x-text="$store.auth.user?.name"></p>
                <p class="text-[11px] text-slate-500 truncate" x-text="$store.auth.user?.email"></p>
            </div>

            {{-- Link navigasi mobile tambahan saat di layar kecil untuk Admin/Guru --}}
            <template x-if="['admin', 'teacher'].includes($store.auth.user?.role)">
                <div class="md:hidden py-1 border-b border-slate-100">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Daftar Santri</a>
                    <template x-if="$store.auth.user?.role === 'admin'">
                        <a href="{{ route('admin.exams.index') }}" class="block rounded-lg px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Kelola Paket &amp; Soal</a>
                    </template>
                </div>
            </template>

            <template x-if="!['admin', 'teacher'].includes($store.auth.user?.role)">
                <a href="{{ route('profile') }}" class="block rounded-lg px-3 py-2 text-xs font-medium hover:bg-slate-100">Hasil &amp; Profil</a>
            </template>
            <button type="button" @click="$store.auth.logout()" class="w-full text-left rounded-lg px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50">Keluar</button>
        </div>
    </div>
</header>