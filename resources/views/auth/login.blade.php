<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIM RSUD Sumenep</title>
    
    <script src="{{ asset('js/tailwind.js') }}"></script>

    <style>
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
        }
    </style>
</head>
<body class="relative min-h-screen bg-[#ece6f6] flex flex-col items-center justify-center p-4 sm:p-6 lg:p-10 text-gray-900 antialiased overflow-x-hidden select-none">

    {{-- 1. WATERMARK TEKS RAKSASA DI OUTSIDE CANVAS (PERSIS SEPERTI GAMBAR ACUAN "Welcome to inSpace") --}}
    <div class="absolute -top-4 left-6 sm:left-12 lg:left-24 pointer-events-none z-0">
        <p class="text-xs sm:text-sm font-semibold tracking-wide text-[#a394c8] ml-1">Selamat Datang di</p>
        <h1 class="text-7xl sm:text-8xl lg:text-[11rem] font-bold text-[#dcd2f0]/60 tracking-tight leading-none -mt-1 sm:-mt-2">
            SIM RSUD
        </h1>
    </div>

    {{-- 2. DEKORASI LINGKARAN & BLUR BACKGROUND SEAMLESS --}}
    <div class="absolute -bottom-16 -right-16 w-[32rem] h-[32rem] bg-purple-300/40 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="absolute top-1/4 -left-20 w-80 h-80 bg-purple-200/50 rounded-full blur-2xl pointer-events-none z-0"></div>

    {{-- 3. MAIN FLOATING CARD --}}
    <div class="relative z-10 w-full max-w-5xl bg-white rounded-[2.5rem] shadow-[0_30px_70px_-15px_rgba(60,30,110,0.2)] overflow-hidden flex flex-col lg:flex-row min-h-[560px] my-auto">
        
        {{-- PANEL KIRI: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-between z-20 bg-white">
            <div>
                {{-- LOGO / ORANGE DOT PERSIS PERSIS GAMBAR ACUAN --}}
                <div class="mb-10">
                    <div class="w-6 h-6 rounded-full bg-[#f2a83b] shadow-sm"></div>
                </div>

                {{-- JUDUL FORM --}}
                <div class="mb-8">
                    <p class="text-xs font-semibold text-[#8b7cb0] tracking-wide">Selamat Datang di</p>
                    <h2 class="text-3xl font-extrabold text-[#2a174d] tracking-tight mt-0.5">
                        SIM RSUD
                    </h2>
                </div>

                @if(session('status'))
                    <div class="mb-6 p-3 rounded-xl bg-green-50 text-xs font-semibold text-green-600 border border-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- FORM INPUT --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- EMAIL FIELD (INPUT BORDER BAWAH ONLY) --}}
                    <div class="relative">
                        <label for="email" class="block text-xs font-semibold text-[#8b7cb0] mb-1">
                            Email Petugas
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               :value="old('email')" 
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="bonjourguys@talk.com" 
                               class="w-full bg-transparent border-b-2 border-[#e6dff2] focus:border-[#9b40ca] text-[#2a174d] text-sm py-2 px-0 outline-none transition-colors duration-200 placeholder-[#c5b8df]" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    {{-- PASSWORD FIELD --}}
                    <div class="relative">
                        <label for="password" class="block text-xs font-semibold text-[#8b7cb0] mb-1">
                            Kata Sandi
                        </label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••" 
                               class="w-full bg-transparent border-b-2 border-[#e6dff2] focus:border-[#9b40ca] text-[#2a174d] text-sm py-2 px-0 outline-none transition-colors duration-200 placeholder-[#c5b8df]" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer text-[#8b7cb0] hover:text-[#2a174d] transition-colors">
                            <input id="remember_me" type="checkbox" class="rounded border-[#e6dff2] text-[#9b40ca] focus:ring-[#9b40ca]" name="remember">
                            <span class="ms-2 font-medium">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    {{-- BUTTON SUBMIT (CAPSULE / PILL SHAPE) --}}
                    <div class="pt-4">
                        <button type="submit" 
                                class="bg-[#a843d0] hover:bg-[#9233be] active:scale-[0.98] text-white text-xs font-bold uppercase tracking-wider py-3.5 px-10 rounded-full shadow-lg shadow-purple-500/25 transition-all duration-200">
                            {{ __('LOGIN') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- FOOTER DI DALAM CARD --}}
            <div class="mt-10 text-xs text-[#8b7cb0]">
                <span>Belum punya akun?</span>
                <a href="#" class="font-bold text-[#a843d0] hover:underline ms-1">Hubungi Admin</a>
            </div>
        </div>

        {{-- PANEL KANAN: ILUSTRASI NIGHT VIOLET DENGAN LEKUKAN ORGANIK (ORGANIC FLUID WAVE) --}}
        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden bg-[#1d093d]">
            
            {{-- OVERLAY ORGANIC WAVE SEPARATOR (MENGHASILKAN LEKUKAN KANVAS PUTIH SAMA PERSIS GAMBAR CONTOH) --}}
            <svg class="absolute top-0 left-0 h-full w-full pointer-events-none fill-white z-20" 
                 viewBox="0 0 500 600" preserveAspectRatio="none">
                <path d="M 0 0 L 230 0 C 280 0, 310 30, 275 90 C 235 160, 205 230, 205 320 C 205 420, 150 510, 110 600 L 0 600 Z" />
            </svg>

            {{-- ARTWORK VECTOR ILUSTRASI (PINTU TERBUKA, CAHAYA BULAN, POHON PINUS, MOUNTAIN & STARRY NIGHT) --}}
            <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#3b1273] via-[#591ca0] to-[#14042b]">
                
                {{-- EFEK BULAN DAN CAHAYA EMAS DI KANAN ATAS --}}
                <div class="absolute top-8 right-12 w-16 h-16 rounded-full bg-gradient-to-tr from-amber-100 to-amber-300 shadow-[0_0_35px_rgba(251,191,36,0.8)] opacity-90"></div>
                <div class="absolute top-6 right-10 w-20 h-20 rounded-full border border-amber-200/20"></div>

                {{-- BINTANG-BINTANG KECIL DI LANGIT --}}
                <div class="absolute top-12 right-36 w-1 h-1 bg-white/70 rounded-full"></div>
                <div class="absolute top-24 right-28 w-1.5 h-1.5 bg-white/80 rounded-full"></div>
                <div class="absolute top-16 right-48 w-1 h-1 bg-white/50 rounded-full"></div>

                {{-- SHAPE PEGUNUNGAN & SILUET POHON PINUS --}}
                <svg class="absolute bottom-0 right-0 w-full h-full text-[#14042b]/60 fill-current" viewBox="0 0 500 600" preserveAspectRatio="none">
                    <!-- Gunung Belakang -->
                    <polygon points="180,600 320,280 460,600" opacity="0.4" fill="#6b21a8" />
                    <polygon points="280,600 400,220 500,600" opacity="0.3" fill="#8b5cf6" />
                </svg>

                {{-- PINTU TERBUKA DENGAN PANCARAN CAHAYA KUNING (CENTRAL ARTWORK) --}}
                <div class="absolute bottom-16 right-12 w-64 h-72 flex flex-col items-end justify-end">
                    <!-- Glow Pintu -->
                    <div class="absolute bottom-4 right-10 w-36 h-48 bg-amber-400/30 blur-xl rounded-full"></div>
                    
                    <!-- Pintu Terbuka Emas -->
                    <div class="relative w-28 h-44 bg-gradient-to-r from-amber-300 via-amber-400 to-amber-200 rounded-t-sm border-2 border-amber-200 shadow-2xl flex items-center justify-center overflow-hidden">
                        <div class="w-full h-full bg-[linear-gradient(90deg,transparent_49%,rgba(245,158,11,0.4)_50%)] bg-[size:12px_100%]"></div>
                    </div>
                    
                    <!-- Tangga & Bangunan Siluet -->
                    <div class="w-48 h-10 bg-[#2d0954] rounded-t-lg -mt-1 shadow-lg border-t border-purple-400/20"></div>
                </div>

            </div>

            {{-- TEKS INFORMASI DI BAGIAN BAWAH PANEL KANAN --}}
            <div class="absolute bottom-8 right-8 z-30 text-right text-white/90 max-w-xs pointer-events-none">
                <span class="inline-block px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold tracking-widest text-amber-300 uppercase border border-white/15 mb-2">
                    RSUD dr. H. Moh. Anwar
                </span>
                <p class="text-xs text-purple-200/80 leading-relaxed drop-shadow">
                    Sistem Informasi Manajemen Rumah Sakit Terpadu
                </p>
            </div>
        </div>

    </div>

    {{-- 4. FOOTER LUAR CARD (PERSIS POSISI TEKS BAWAH "Dont have an account? Sign up") --}}
    <div class="w-full max-w-5xl flex justify-between items-center text-xs text-[#a394c8] px-4 mt-6 z-10">
        <div>
            <span>Belum punya akun?</span>
            <a href="#" class="font-bold text-[#9b40ca] hover:underline ms-1">Hubungi Admin</a>
        </div>
        <div>
            <p>&copy; {{ date('Y') }} RSUD dr. H. Moh. Anwar Sumenep</p>
        </div>
    </div>

</body>
</html>