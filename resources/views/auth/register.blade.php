<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Log In — Talent Mapping</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen max-h-screen overflow-hidden bg-white font-['Montserrat',sans-serif] antialiased select-none m-0 p-0">
    
    <div x-data="authSwitcher()" class="w-full h-full relative overflow-hidden flex">

        <!-- ==================== LAYER 1 (z-0): LEFT SIDE BACKGROUND (SLIDES IN FOR LOGIN) ==================== -->
        <div class="hidden md:block w-1/2 h-full absolute top-0 left-0 z-0 overflow-hidden transition-all duration-500 ease-in-out"
             :class="displayMode === 'register' ? '-translate-x-full opacity-0 pointer-events-none' : 'translate-x-0 opacity-100'">
            
            <img src="{{ asset('images/landing/background1.jpg') }}" 
                 alt="Students Playing" 
                 class="w-full h-full object-cover object-center filter saturate-110 contrast-105 pointer-events-none">
            
            <div class="absolute inset-0 pointer-events-none" 
                 style="background: linear-gradient(189.69deg, rgba(255, 188, 1, 0.8) 20.76%, rgba(254, 110, 65, 0.8) 67.93%);">
            </div>

            <!-- Logo -->
            <div class="absolute top-10 left-10 z-20">
                <img src="{{ asset('images/landing/logo.svg') }}" alt="Talent Mapping" class="h-16 w-auto">
            </div>

            <!-- Text & Blue Blocks -->
            <div class="absolute top-32 left-10 z-20 max-w-lg text-white">
                <h1 class="text-5xl font-extrabold leading-tight mb-6">
                    Masa Depan Besar<br>
                    <span class="bg-[#0984E3] px-2">Dimulai dari</span><br>
                    <span class="bg-[#0984E3] px-2">Mengenal Diri Sendiri</span>
                </h1>
                <p class="text-lg font-medium mb-8">
                    Talent Mapping membantu <strong>mengidentifikasi kekuatan, minat, dan potensi unikmu</strong> melalui serangkaian asesmen yang dirancang khusus untuk pelajar.
                </p>
                <p class="text-lg font-bold">
                    Belajar lebih terarah. Berkembang lebih percaya diri.
                </p>
            </div>

            <!-- Circle SVG Overlay -->
            <img src="{{ asset('images/register/circle.svg') }}" 
                 class="absolute -bottom-[30%] -right-[20%] w-[80%] h-auto pointer-events-none z-30 opacity-90">
        </div>

        <!-- ==================== LAYER 2 (z-0): RIGHT SIDE BACKGROUND (FADES IN FOR REGISTER) ==================== -->
        <div class="absolute inset-0 z-0 overflow-hidden transition-opacity duration-500 ease-in-out pointer-events-none"
             :class="displayMode === 'login' ? 'opacity-0' : 'opacity-100'">
            <img src="{{ asset('images/landing/background.jpg') }}" 
                 alt="Students" 
                 class="w-full h-full object-cover object-center pointer-events-none">
            <div class="absolute inset-0 pointer-events-none" 
                 style="background: linear-gradient(189.69deg, rgba(255, 188, 1, 0.8) 20.76%, rgba(254, 110, 65, 0.8) 67.93%);">
            </div>
        </div>

        <!-- ==================== LAYER 3 (z-20): FORM CONTAINER (MOVES LEFT/RIGHT AND CENTERS) ==================== -->
        <div class="absolute inset-0 w-full h-full flex items-center z-20 pointer-events-none p-4 sm:p-6 lg:p-10"
             :class="displayMode === 'login' ? 'justify-end' : 'justify-center'">
            <div class="w-full md:w-1/2 h-full flex items-center justify-center pointer-events-auto">
                
                <!-- CARD FORM (Drops down & back up for animation) -->
                              <!-- CARD FORM (Made smaller to fit perfectly) -->
                              <div class="w-full max-w-[420px] max-h-[92vh] bg-white rounded-[24px] p-5 sm:p-6 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.15)] border border-slate-100/90 flex flex-col justify-center my-auto transition-all duration-500 ease-in-out transform"
                     :class="cardState === 'visible' ? 'translate-y-0 opacity-100' : 'translate-y-[130vh] opacity-0 pointer-events-none'">
                    
                    <!-- ================= VIEW LOGIN ================= -->
                    <template x-if="displayMode === 'login'">
                        <div>
                            <img src="{{ asset('images/landing/logo.svg') }}" alt="Talent Mapping" class="h-10 mx-auto mb-4">
                            <div class="text-center space-y-2 mb-4 sm:mb-5">
                                <h1 class="text-2xl sm:text-3xl font-bold text-[#FBBF24] leading-tight">Selamat Datang 👋</h1>
                                <p class="text-xs sm:text-sm font-normal text-slate-800">
                                    New To Talent Mapping? 
                                    <button type="button" @click="switchMode('register')" :disabled="isBusy" class="text-[#5B50E5] font-semibold hover:underline underline-offset-4 transition-colors disabled:opacity-50">
                                        Sign Up for free
                                    </button>
                                </p>
                            </div>

                            <div x-show="error" x-cloak x-text="error" class="mb-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-2 text-center"></div>

                            <form @submit.prevent="submitLogin" class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Email<span class="text-rose-500">*</span></label>
                                    <input type="email" x-model="loginData.email" required placeholder="Masukan Email kamu."
                                           class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Password<span class="text-rose-500">*</span></label>
                                    <input type="password" x-model="loginData.password" required placeholder="Masukan Password kamu."
                                           class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                </div>
                                
                                <div class="flex items-center justify-between pt-1">
                                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                        <input type="checkbox" class="rounded border-slate-300 text-[#5B50E5] focus:ring-[#5B50E5]">
                                        Ingatkan saya
                                    </label>
                                    <button type="button" class="text-xs text-[#5B50E5] font-semibold hover:underline underline-offset-4">
                                        Lupa password?
                                    </button>
                                </div>

                                <div class="pt-1 sm:pt-2">
                                    <button type="submit" :disabled="loading" 
                                            class="w-full h-11 rounded-full font-bold text-white text-sm sm:text-base tracking-wide transition-all shadow-lg shadow-blue-500/25 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3 bg-[#6C70EB] hover:bg-[#5B50E5]">
                                        <span x-show="!loading">Let's Get Started!</span>
                                        <span x-show="loading">Memproses...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>

                    <!-- ================= VIEW REGISTER ================= -->
                    <template x-if="displayMode === 'register'">
                        <div>
                            <img src="{{ asset('images/landing/logo.svg') }}" alt="Talent Mapping" class="h-10 mx-auto mb-4">
                            <div class="text-center space-y-2 mb-4 sm:mb-5">
                                <h1 class="text-2xl sm:text-3xl font-bold text-[#FBBF24] leading-tight">Sign Up</h1>
                                <p class="text-xs sm:text-sm font-normal text-slate-800">
                                    Already have an account? 
                                    <button type="button" @click="switchMode('login')" :disabled="isBusy" class="text-[#5B50E5] font-semibold hover:underline underline-offset-4 transition-colors disabled:opacity-50">
                                        Log In here
                                    </button>
                                </p>
                            </div>

                            <div x-show="error" x-cloak x-text="error" class="mb-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-2 text-center"></div>

                            <form @submit.prevent="submitRegister" class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Nama Lengkap<span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="registerData.name" required placeholder="Masukan Nama lengkap kamu."
                                           class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Email<span class="text-rose-500">*</span></label>
                                    <input type="email" x-model="registerData.email" required placeholder="Masukan Email kamu."
                                           class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Buat password<span class="text-rose-500">*</span></label>
                                    <input type="password" x-model="registerData.password" required minlength="8" placeholder="Buat password kamu."
                                           class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Usia<span class="text-rose-500">*</span></label>
                                        <input type="number" x-model="registerData.age" placeholder="Tulis umur kamu."
                                               class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Asal Sekolah<span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="registerData.school" placeholder="Nama Sekolah kamu."
                                               class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#5B50E5] focus:border-transparent transition-all shadow-xs">
                                    </div>
                                </div>
                                <div class="pt-1 sm:pt-2">
                                    <button type="submit" :disabled="loading" 
                                            class="w-full h-11 rounded-full font-bold text-white text-sm sm:text-base tracking-wide transition-all shadow-lg shadow-blue-500/25 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3 bg-[#6C70EB] hover:bg-[#5B50E5]">
                                        <span x-show="!loading">Let's Get Started!</span>
                                        <span x-show="loading">Memproses...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>

                </div>
            </div>
        </div>

    </div>

    <script>
        function authSwitcher() {
            const initialMode = window.location.pathname.includes('register') ? 'register' : 'login';
            return {
                mode: initialMode,
                displayMode: initialMode,
                containerPosition: initialMode,
                cardState: 'visible',
                isBusy: false,
                loading: false,
                error: null,
                loginData: { email: '', password: '' },
                registerData: { name: '', email: '', password: '', age: '', school: '' },

                switchMode(target) {
                    if (this.mode === target || this.isBusy) return;
                    this.isBusy = true;
                    this.error = null;
                
                    document.title = target === 'login' ? 'Log In — Talent Mapping' : 'Sign Up — Talent Mapping';
                
                    // 1. FASE 1: Form drops down to hide
                    this.cardState = 'hidden';
                
                    setTimeout(() => {
                        // 2. FASE 2: Swap layouts (This reverses perfectly: Register hides left panel & centers form, Login slides left panel back in & moves form right)
                        this.mode = target;
                        this.containerPosition = target;
                        this.displayMode = target;
                        window.history.pushState({}, '', '/' + target);
                
                        // 3. FASE 3: Form drops back up into its new position
                        setTimeout(() => {
                            this.cardState = 'visible';
                            this.isBusy = false;
                        }, 500);
                    }, 500);
                },

                async submitLogin() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/api/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.loginData)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Login gagal');

                        const token = data.token || data.access_token;
                        const user = data.user;

                        localStorage.setItem('ts_token', token);
                        localStorage.setItem('token', token);
                        if (user) {
                            localStorage.setItem('ts_user', JSON.stringify(user));
                        }

                        if (user?.role === 'admin' || user?.role === 'teacher') {
                            window.location.href = '/admin/dashboard';
                        } else {
                            window.location.href = '/beranda';
                        }
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async submitRegister() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/api/register', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.registerData)
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Registrasi gagal');

                        const token = data.token || data.access_token;
                        const user = data.user;

                        localStorage.setItem('ts_token', token);
                        localStorage.setItem('token', token);
                        if (user) {
                            localStorage.setItem('ts_user', JSON.stringify(user));
                        }

                        window.location.href = '/beranda';
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>