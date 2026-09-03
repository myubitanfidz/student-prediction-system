<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title x-data x-text="mode === 'login' ? 'Log In — Talent Mapping' : 'Sign Up — Talent Mapping'">Log In — Talent Mapping</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen max-h-screen overflow-hidden bg-white font-['Montserrat',sans-serif] antialiased select-none m-0 p-0">
    
    <div x-data="authSwitcher()" class="w-full h-full relative overflow-hidden flex">

        <!-- ==================== LAYER 1 (z-0): GAMBAR SLIDER BERGERAK (0.35 Detik) ==================== -->
        <div class="hidden md:block w-1/2 h-full absolute top-0 left-0 bg-slate-100 overflow-hidden z-0 transition-transform duration-350 ease-in-out"
             :class="mode === 'login' ? 'translate-x-0' : 'translate-x-full'">
            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200&auto=format&fit=crop" 
                 alt="Students Playing" 
                 class="w-full h-full object-cover object-center filter saturate-110 contrast-105 pointer-events-none">
        </div>

        <!-- ==================== LAYER 2 (z-10): LINGKARAN UNGU BERGERAK (0.35 Detik) ==================== -->
        <div class="hidden md:block absolute rounded-full bg-[#6C70EB] pointer-events-none z-10 transition-all duration-350 ease-in-out"
             :style="mode === 'login' 
                ? 'width: min(55vw, 680px); height: min(55vw, 680px); left: 55%; bottom: -55vh; transform: translateX(-50%);' 
                : 'width: min(55vw, 680px); height: min(55vw, 680px); left: 45%; bottom: -55vh; transform: translateX(-50%);'">
        </div>

        <!-- ==================== LAYER 3 (z-20): CONTAINER FORM ==================== -->
        <div class="absolute inset-0 w-full h-full flex items-center z-20 pointer-events-none p-4 sm:p-6 lg:p-10"
             :class="containerPosition === 'login' ? 'justify-end' : 'justify-start'">
            <div class="w-full md:w-1/2 h-full flex items-center justify-center pointer-events-auto">
                
                <!-- CARD FORM (Durasi 0.5 detik meluncur ke bawah & naik ke atas) -->
                <div class="w-full max-w-[540px] max-h-[92vh] bg-white rounded-[24px] p-6 sm:p-10 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.08)] border border-slate-100/90 flex flex-col justify-center my-auto transition-all duration-150 ease-in-out transform"
                     :class="cardState === 'visible' ? 'translate-y-0 opacity-100' : 'translate-y-[130vh] opacity-0 pointer-events-none'">
                    
                    <!-- ================= VIEW LOGIN ================= -->
                    <template x-if="displayMode === 'login'">
                        <div>
                            <div class="text-center space-y-2 mb-6 sm:mb-8">
                                <h1 class="text-3xl sm:text-[40px] font-bold text-[#FBBF24] leading-tight">Log In</h1>
                                <p class="text-sm sm:text-base font-normal text-slate-800">
                                    New To Talent Mapping? 
                                    <button type="button" @click="switchMode('register')" :disabled="isBusy" class="text-[#5B50E5] font-semibold hover:underline underline-offset-4 transition-colors disabled:opacity-50">
                                        Sign Up for free
                                    </button>
                                </p>
                            </div>

                            <div x-show="error" x-cloak x-text="error" class="mb-4 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-3 text-center"></div>

                            <form @submit.prevent="submitLogin" class="space-y-4 sm:space-y-5">
                                <div>
                                    <input type="email" x-model="loginData.email" required placeholder="Email Address"
                                           class="w-full h-12 sm:h-14 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div>
                                    <input type="password" x-model="loginData.password" required placeholder="Password"
                                           class="w-full h-12 sm:h-14 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div class="pt-2 sm:pt-4">
                                    <button type="submit" :disabled="loading" 
                                            class="w-full h-14 sm:h-16 rounded-full font-bold text-white text-base sm:text-lg tracking-wide transition-all shadow-lg shadow-orange-500/25 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                                            style="border: 1px solid #FE6E41; background: linear-gradient(96.05deg, #FFBC01 -13.58%, #FE6E41 97.28%);">
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
                            <div class="text-center space-y-2 mb-5 sm:mb-6">
                                <h1 class="text-3xl sm:text-[40px] font-bold text-[#FBBF24] leading-tight">Sign Up</h1>
                                <p class="text-sm sm:text-base font-normal text-slate-800">
                                    Already have an account? 
                                    <button type="button" @click="switchMode('login')" :disabled="isBusy" class="text-[#5B50E5] font-semibold hover:underline underline-offset-4 transition-colors disabled:opacity-50">
                                        Log In here
                                    </button>
                                </p>
                            </div>

                            <div x-show="error" x-cloak x-text="error" class="mb-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-2.5 text-center"></div>

                            <form @submit.prevent="submitRegister" class="space-y-3.5 sm:space-y-4">
                                <div>
                                    <input type="text" x-model="registerData.name" required placeholder="Full Name"
                                           class="w-full h-11 sm:h-13 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div>
                                    <input type="email" x-model="registerData.email" required placeholder="Email Address"
                                           class="w-full h-11 sm:h-13 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div>
                                    <input type="password" x-model="registerData.password" required minlength="8" placeholder="Password (min. 8 characters)"
                                           class="w-full h-11 sm:h-13 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                                </div>
                                <div class="pt-2 sm:pt-3">
                                    <button type="submit" :disabled="loading" 
                                            class="w-full h-14 sm:h-16 rounded-full font-bold text-white text-base sm:text-lg tracking-wide transition-all shadow-lg shadow-orange-500/25 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                                            style="border: 1px solid #FE6E41; background: linear-gradient(96.05deg, #FFBC01 -13.58%, #FE6E41 97.28%);">
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
                registerData: { name: '', email: '', password: '' },

                switchMode(target) {
                    if (this.mode === target || this.isBusy) return;
                    this.isBusy = true;
                    this.error = null;

                    // 1. FASE 1: Card meluncur turun sampai keluar layar bawah (0.5 detik)
                    this.cardState = 'hidden';

                    setTimeout(() => {
                        // 2. FASE 2: Ganti posisi container & mulai animasi pergeseran gambar/lingkaran (0.5 detik)
                        this.mode = target;
                        this.containerPosition = target;
                        this.displayMode = target;
                        window.history.pushState({}, '', '/' + target);

                        setTimeout(() => {
                            // 3. FASE 3: Pergeseran selesai, card meluncur naik dari bawah ke atas (0.5 detik)
                            this.cardState = 'visible';
                            this.isBusy = false;
                        }, 400); // Tunggu sampai animasi geser 0.4s tuntas
                    }, 400); // Tunggu sampai card turun 0.4s tuntas
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
                        localStorage.setItem('ts_token', data.token);
                        window.location.href = data.user?.role === 'admin' ? '/admin/exams' : '/beranda';
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
                        localStorage.setItem('ts_token', data.token);
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