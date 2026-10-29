<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIM RSUD Sumenep</title>
    
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Fallback for preview, remove if using local tailwind -->

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; 
        }

        /* Custom Animations */
        @keyframes twinkle {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-twinkle { animation: twinkle 3s ease-in-out infinite; }
        .animate-twinkle-delay { animation: twinkle 4s ease-in-out infinite 1s; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-slide-in { animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        /* Input Focus Border Animation */
        .input-group input:focus ~ .input-border {
            width: 100%;
            background-color: #9b40ca;
        }
    </style>
</head>
<body class="relative min-h-screen bg-[#f3eff9] flex flex-col items-center justify-center p-4 sm:p-6 lg:p-10 text-gray-900 antialiased overflow-hidden select-none">

    {{-- 1. WATERMARK TEKS RAKSASA --}}
    <div class="absolute -top-4 left-6 sm:left-12 lg:left-24 pointer-events-none z-0 opacity-40">
        <p class="text-xs sm:text-sm font-bold tracking-widest text-[#b8abc9] ml-2 uppercase">Portal Terpadu</p>
        <h1 class="text-7xl sm:text-8xl lg:text-[11rem] font-extrabold text-white tracking-tighter leading-none -mt-2 drop-shadow-md">
            SIM RSUD
        </h1>
    </div>

    {{-- 2. DEKORASI LINGKARAN & BLUR BACKGROUND --}}
    <div class="absolute -bottom-20 -right-20 w-[40rem] h-[40rem] bg-purple-300/30 rounded-full blur-[100px] pointer-events-none z-0"></div>
    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-fuchsia-200/30 rounded-full blur-[80px] pointer-events-none z-0"></div>

    {{-- 3. MAIN FLOATING CARD --}}
    <div class="relative z-10 w-full max-w-[1040px] bg-white/90 backdrop-blur-xl rounded-[2.5rem] shadow-[0_40px_80px_-20px_rgba(60,30,110,0.15)] overflow-hidden flex flex-col lg:flex-row min-h-[600px] my-auto animate-slide-in border border-white/50">
        
        {{-- PANEL KIRI: FORM LOGIN --}}
        <div class="w-full lg:w-[45%] p-8 sm:p-12 lg:p-14 flex flex-col justify-center z-20 bg-white relative">
            
            {{-- LOGO & HEADER --}}
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#f2a83b] to-[#e68a15] shadow-lg shadow-orange-500/30 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-[#2a174d] tracking-wide">RSUD Dr. H. Moh. Anwar</span>
                </div>

                <div>
                    <p class="text-sm font-semibold text-[#8b7cb0] tracking-wide mb-1">Selamat Datang di</p>
                    <h2 class="text-4xl font-extrabold text-[#2a174d] tracking-tight">
                        SIM RSUD<span class="text-[#f2a83b]">.</span>
                    </h2>
                </div>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50/80 backdrop-blur-sm flex items-start gap-3 border border-green-100">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs font-semibold text-green-700 leading-relaxed">{{ session('status') }}</p>
                </div>
            @endif

            {{-- FORM INPUT --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-7">
                @csrf

                {{-- EMAIL FIELD --}}
                <div class="relative input-group group">
                    <label for="email" class="block text-xs font-bold text-[#8b7cb0] mb-2 uppercase tracking-wider">Email Petugas</label>
                    <div class="relative flex items-center">
                        <svg class="absolute left-0 w-5 h-5 text-[#c5b8df] group-focus-within:text-[#9b40ca] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email anda..." class="w-full bg-transparent border-b-2 border-[#e6dff2] text-[#2a174d] text-sm py-2 pl-8 pr-0 outline-none transition-all duration-300 placeholder-[#d4cbe5]" />
                        <div class="input-border absolute bottom-0 left-0 h-[2px] w-0 bg-transparent transition-all duration-300 ease-out"></div>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500 font-medium" />
                </div>

                {{-- PASSWORD FIELD --}}
                <div class="relative input-group group">
                    <label for="password" class="block text-xs font-bold text-[#8b7cb0] mb-2 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative flex items-center">
                        <svg class="absolute left-0 w-5 h-5 text-[#c5b8df] group-focus-within:text-[#9b40ca] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full bg-transparent border-b-2 border-[#e6dff2] text-[#2a174d] text-sm py-2 pl-8 pr-0 outline-none transition-all duration-300 placeholder-[#d4cbe5]" />
                        <div class="input-border absolute bottom-0 left-0 h-[2px] w-0 bg-transparent transition-all duration-300 ease-out"></div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500 font-medium" />
                </div>

                {{-- REMEMBER ME --}}
                <div class="flex items-center justify-between text-xs pt-2">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <div class="relative flex items-center justify-center w-4 h-4 rounded border-2 border-[#e6dff2] group-hover:border-[#9b40ca] transition-colors">
                            <input id="remember_me" type="checkbox" class="peer opacity-0 absolute w-full h-full cursor-pointer" name="remember">
                            <svg class="w-3 h-3 text-[#9b40ca] opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="ms-2 font-semibold text-[#8b7cb0] group-hover:text-[#2a174d] transition-colors">{{ __('Ingat Saya') }}</span>
                    </label>
                    <a href="#" class="font-semibold text-[#9b40ca] hover:text-[#a843d0] hover:underline transition-all">Lupa Sandi?</a>
                </div>

                {{-- BUTTON SUBMIT --}}
                <div class="pt-6">
                    <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-[#9233be] to-[#a843d0] hover:from-[#7e25a8] hover:to-[#9233be] shadow-lg shadow-purple-500/30 active:scale-[0.98] transition-all duration-200 overflow-hidden">
                        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                        <span class="relative flex items-center gap-2 tracking-wider">
                            MASUK SISTEM
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- PANEL KANAN: ILUSTRASI NIGHT VIOLET --}}
        <div class="hidden lg:block lg:w-[55%] relative overflow-hidden bg-[#120524]">
            
            {{-- OVERLAY ORGANIC WAVE SEPARATOR --}}
            <svg class="absolute top-0 -left-1 h-full w-[120px] pointer-events-none fill-white z-20" viewBox="0 0 100 600" preserveAspectRatio="none">
                <path d="M 0 0 L 0 600 L 40 600 C 60 500, 100 400, 60 300 C 20 200, 60 100, 40 0 Z" />
            </svg>

            {{-- ARTWORK VECTOR ILUSTRASI --}}
            <div class="absolute inset-0 z-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#4c198a] via-[#240b4a] to-[#0f041c]">
                
                {{-- BULAN DAN GLOW --}}
                <div class="absolute top-12 right-16 w-20 h-20 rounded-full bg-gradient-to-br from-amber-100 to-amber-400 shadow-[0_0_60px_rgba(251,191,36,0.6)] opacity-95"></div>
                <!-- Cincin bulan -->
                <div class="absolute top-8 right-12 w-28 h-28 rounded-full border-[1px] border-amber-200/10 mix-blend-overlay"></div>
                <div class="absolute top-4 right-8 w-36 h-36 rounded-full border-[1px] border-amber-200/5 mix-blend-overlay"></div>

                {{-- BINTANG-BINTANG --}}
                <div class="absolute top-16 right-48 w-1.5 h-1.5 bg-white rounded-full animate-twinkle shadow-[0_0_5px_#fff]"></div>
                <div class="absolute top-32 right-32 w-1 h-1 bg-white/80 rounded-full animate-twinkle-delay"></div>
                <div class="absolute top-20 right-80 w-2 h-2 bg-purple-200/70 rounded-full animate-twinkle"></div>
                <div class="absolute top-40 right-64 w-1 h-1 bg-white/60 rounded-full animate-twinkle-delay"></div>

                {{-- PEGUNUNGAN DENGAN GRADASI HALUS --}}
                <div class="absolute bottom-0 right-0 w-full h-[40%] bg-gradient-to-t from-[#0a0214] to-transparent z-10"></div>
                <svg class="absolute bottom-0 right-0 w-full h-[55%] fill-current z-0" viewBox="0 0 500 300" preserveAspectRatio="none">
                    <!-- Gunung Belakang -->
                    <path d="M 50 300 L 250 120 L 450 300 Z" class="text-[#3b1273] opacity-60" />
                    <!-- Gunung Depan -->
                    <path d="M 180 300 L 380 80 L 550 300 Z" class="text-[#260952] opacity-90" />
                </svg>

                {{-- PINTU TERBUKA (CENTRAL ARTWORK) - Animasi Floating --}}
                <div class="absolute bottom-20 right-20 w-64 h-72 flex flex-col items-end justify-end animate-float z-20">
                    <!-- Glow Ekstra Belakang Pintu -->
                    <div class="absolute bottom-10 right-12 w-40 h-56 bg-amber-500/40 blur-[40px] rounded-full"></div>
                    
                    <!-- Pintu Terbuka -->
                    <div class="relative w-32 h-52 bg-gradient-to-br from-amber-200 via-amber-400 to-amber-600 rounded-t-full border-4 border-amber-100/50 shadow-[0_0_50px_rgba(245,158,11,0.5)] flex items-center justify-center overflow-hidden">
                        <!-- Pola Garis Cahaya di dalam pintu -->
                        <div class="w-full h-full bg-[linear-gradient(90deg,transparent_45%,rgba(255,255,255,0.4)_50%,transparent_55%)] bg-[size:100%_100%] opacity-80"></div>
                        <!-- Siluet orang (opsional untuk menambah detail) -->
                        <div class="absolute bottom-0 w-6 h-14 bg-[#120524] rounded-t-full opacity-90"></div>
                    </div>
                    
                    <!-- Tangga/Platform Bawah Pintu -->
                    <div class="w-48 h-8 bg-gradient-to-b from-[#2d0954] to-[#14042b] rounded-t-xl -mt-2 shadow-2xl border-t-2 border-purple-400/30 flex items-center justify-center">
                        <div class="w-32 h-1 bg-amber-400/30 blur-sm"></div>
                    </div>
                </div>

            </div>

            {{-- TEKS INFORMASI KANAN BAWAH --}}
            <div class="absolute bottom-10 right-10 z-30 text-right max-w-sm pointer-events-none bg-black/20 p-5 rounded-2xl backdrop-blur-md border border-white/10">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-500/20 rounded-lg text-[10px] font-bold tracking-widest text-amber-300 uppercase border border-amber-400/30 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Versi 2.0
                </span>
                <h3 class="text-xl font-bold text-white mb-1">RSUD dr. H. Moh. Anwar</h3>
                <p class="text-sm text-purple-200/80 leading-relaxed font-medium">
                    Sistem Informasi Manajemen Terpadu untuk Pelayanan Kesehatan yang Lebih Baik.
                </p>
            </div>
        </div>

    </div>

    {{-- 4. FOOTER LUAR CARD --}}
    <div class="w-full max-w-[1040px] flex flex-col sm:flex-row justify-between items-center text-xs text-[#8b7cb0] px-6 mt-8 z-10 font-medium">
        <div class="mb-2 sm:mb-0">
            <span>Butuh bantuan akses?</span>
            <a href="#" class="font-bold text-[#9b40ca] hover:text-[#a843d0] hover:underline ms-1 transition-colors">Hubungi Tim IT</a>
        </div>
        <div class="flex items-center gap-2">
            <span>&copy; {{ date('Y') }} RSUD dr. H. Moh. Anwar Sumenep.</span>
            <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-[#8b7cb0]"></span>
            <span class="hidden sm:inline">Dilindungi Undang-Undang.</span>
        </div>
    </div>

</body>
</html>