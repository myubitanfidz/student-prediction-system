<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up — Talent Mapping</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen max-h-screen overflow-hidden bg-white font-['Montserrat',sans-serif] antialiased select-none m-0 p-0">
    
    <div x-data="registerPage" class="w-full h-full relative overflow-hidden flex flex-col md:flex-row">
        
        <!-- ==================== LAYER 1 (z-0): BACKGROUND KIRI (PUTIH) & KANAN (GAMBAR) ==================== -->
        <!-- Sisi Kiri: Putih Polos -->
        <div class="w-full md:w-1/2 h-full bg-white relative z-0 shrink-0"></div>

        <!-- Sisi Kanan: Gambar Hero -->
        <div class="hidden md:block w-1/2 h-full relative bg-slate-100 overflow-hidden z-0 shrink-0">
            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200&auto=format&fit=crop" 
                 alt="Students Playing" 
                 class="w-full h-full object-cover object-center filter saturate-110 contrast-105 pointer-events-none">
        </div>

        <!-- ==================== LAYER 2 (z-10): LINGKARAN UNGU TENGAH (BERGESER KE KIRI) ==================== -->
        <div class="hidden md:block absolute rounded-full bg-[#6C70EB] pointer-events-none z-10"
             style="width: min(55vw, 680px); height: min(55vw, 680px); left: 45%; bottom: -55vh; transform: translateX(-50%);">
        </div>

        <!-- ==================== LAYER 3 (z-20): CONTAINER FORM REGISTER DI SISI KIRI ==================== -->
        <div class="absolute inset-0 w-full h-full flex items-center justify-start z-20 pointer-events-none p-4 sm:p-6 lg:p-10">
            <div class="w-full md:w-1/2 h-full flex items-center justify-center pointer-events-auto">
                
                <!-- CARD REGISTER -->
                <div class="w-full max-w-[540px] max-h-[92vh] bg-white rounded-[24px] p-6 sm:p-10 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.08)] border border-slate-100/90 flex flex-col justify-center my-auto">
                    
                    <!-- Header -->
                    <div class="text-center space-y-2 mb-5 sm:mb-6">
                        <h1 class="text-3xl sm:text-[40px] font-bold text-[#FBBF24] leading-tight">Sign Up</h1>
                        <p class="text-sm sm:text-base font-normal text-slate-800">
                            Already have an account? 
                            <a href="/login" class="text-[#5B50E5] font-semibold hover:underline underline-offset-4 transition-colors">
                                Log In here
                            </a>
                        </p>
                    </div>

                    <!-- Alert Error -->
                    <div x-show="error" 
                         x-cloak 
                         x-transition 
                         x-text="error" 
                         class="mb-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl p-2.5 text-center">
                    </div>

                    <!-- Form Inputs -->
                    <form @submit.prevent="submit" class="space-y-3.5 sm:space-y-4">
                        <div>
                            <input type="text" 
                                   x-model="name" 
                                   required 
                                   placeholder="Full Name"
                                   class="w-full h-11 sm:h-13 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                        </div>

                        <div>
                            <input type="email" 
                                   x-model="email" 
                                   required 
                                   placeholder="Email Address"
                                   class="w-full h-11 sm:h-13 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                        </div>

                        <div>
                            <input type="password" 
                                   x-model="password" 
                                   required 
                                   minlength="8" 
                                   placeholder="Password (min. 8 characters)"
                                   class="w-full h-11 sm:h-13 rounded-[16px] border border-slate-300 px-5 text-sm sm:text-base text-slate-800 placeholder:text-slate-400 placeholder:italic font-medium focus:outline-none focus:ring-2 focus:ring-[#FBBF24] focus:border-transparent transition-all shadow-xs">
                        </div>

                        <div class="pt-2 sm:pt-3">
                            <button type="submit" 
                                    :disabled="loading" 
                                    class="w-full h-14 sm:h-16 rounded-full font-bold text-white text-base sm:text-lg tracking-wide transition-all shadow-lg shadow-orange-500/25 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                                    style="border: 1px solid #FE6E41; background: linear-gradient(96.05deg, #FFBC01 -13.58%, #FE6E41 97.28%);">
                                <span x-show="!loading">Let's Get Started!</span>
                                <span x-show="loading" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memproses...</span>
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

</body>
</html>