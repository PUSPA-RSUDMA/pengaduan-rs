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
<body class="relative min-h-screen bg-[#ebe5f7] flex items-center justify-center p-4 sm:p-6 lg:p-10 text-gray-900 antialiased overflow-x-hidden select-none">

    {{-- 1. WATERMARK TEKS RAKSASA DI OUTSIDE CANVAS (PERSIS GAMBAR CONTOH) --}}
    <div class="absolute top-4 left-6 lg:top-8 lg:left-16 pointer-events-none z-0">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#a89bc9]">Selamat Datang di</p>
        <h1 class="text-6xl sm:text-7xl lg:text-8xl font-black text-[#d8cdf0]/60 tracking-tight leading-none mt-1">
            SIM RSUD
        </h1>
    </div>

    {{-- 2. ELEMEN DEKORATIF BACKGROUND OUTSIDE CARD (AGAR BERSATU/SEAMLESS) --}}
    <div class="absolute -bottom-20 -right-20 w-[30rem] h-[30rem] bg-gradient-to-tr from-[#a251d1]/30 to-[#5b1f9b]/20 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="absolute top-1/3 -left-20 w-80 h-80 bg-purple-300/30 rounded-full blur-2xl pointer-events-none z-0"></div>

    {{-- 3. MAIN CONTAINER CARD (MELAYANG DENGAN SOFT SHADOW & CORNER ROUNDED) --}}
    <div class="relative z-10 w-full max-w-5xl bg-white rounded-[2.5rem] shadow-[0_25px_60px_-15px_rgba(80,50,135,0.2)] overflow-hidden flex flex-col lg:flex-row min-h-[560px]">
        
        {{-- PANEL KIRI: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-14 flex flex-col justify-between z-20 bg-white">
            <div>
                {{-- LOGO ORANGE DOT --}}
                <div class="mb-8">
                    <div class="w-5 h-5 rounded-full bg-[#f4a236] shadow-sm"></div>
                </div>

                {{-- JUDUL FORM --}}
                <div class="mb-8">
                    <p class="text-xs font-bold text-[#b0a5d1] tracking-wider uppercase">Selamat Datang di</p>
                    <h2 class="text-3xl font-extrabold text-[#221543] tracking-tight mt-1">
                        SIM RSUD
                    </h2>
                </div>

                @if(session('status'))
                    <div class="mb-5 p-3 rounded-xl bg-green-50 text-xs font-semibold text-green-600 border border-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- FORM INPUT --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- EMAIL FIELD --}}
                    <div class="relative">
                        <label for="email" class="block text-[11px] font-bold text-[#b0a5d1] tracking-wider uppercase mb-1">
                            Email Petugas
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               :value="old('email')" 
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="admin@rsud.com" 
                               class="w-full bg-transparent border-b border-[#e5def0] focus:border-[#a251d1] text-[#221543] text-sm py-2 px-0 outline-none transition-colors duration-200 placeholder-[#c7bedf]" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    {{-- PASSWORD FIELD --}}
                    <div class="relative">
                        <label for="password" class="block text-[11px] font-bold text-[#b0a5d1] tracking-wider uppercase mb-1">
                            Kata Sandi
                        </label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••" 
                               class="w-full bg-transparent border-b border-[#e5def0] focus:border-[#a251d1] text-[#221543] text-sm py-2 px-0 outline-none transition-colors duration-200 placeholder-[#c7bedf]" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer text-[#b0a5d1] hover:text-[#221543] transition-colors">
                            <input id="remember_me" type="checkbox" class="rounded border-[#e5def0] text-[#a251d1] focus:ring-[#a251d1]" name="remember">
                            <span class="ms-2 font-medium">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    {{-- BUTTON SUBMIT (CAPSULE / PILL SHAPE) --}}
                    <div class="pt-2">
                        <button type="submit" 
                                class="bg-[#a844cf] hover:bg-[#9233b8] active:scale-[0.98] text-white text-xs font-bold uppercase tracking-wider py-3.5 px-10 rounded-full shadow-md shadow-purple-400/30 transition-all duration-200">
                            {{ __('LOGIN') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- FOOTER DI DALAM CARD --}}
            <div class="mt-8 text-xs text-[#b0a5d1]">
                <span>Belum punya akun?</span>
                <a href="#" class="font-bold text-[#a844cf] hover:underline ms-1">Hubungi Admin</a>
            </div>
        </div>

        {{-- PANEL KANAN: GAMBAR DENGAN FLUID ORGANIC WAVE MASK (MENYATU ORGANIK) --}}
        <div class="hidden lg:block lg:w-1/2 relative bg-cover bg-center overflow-hidden"
             style="background-image: url('{{ asset('images/gedung-rsud.png') }}');">
            
            {{-- OVERLAY GRADIENT SERUPA VIOLET-NIGHT DESAIN ACUAN --}}
            <div class="absolute inset-0 bg-gradient-to-br from-[#3c1477]/85 via-[#5c1c9c]/80 to-[#19063b]/90"></div>

            {{-- SVG ORGANIC FLUID WAVE CURVE (LUKISAN LEKUKAN ORGANIK MANTAP) --}}
            <svg class="absolute top-0 left-0 h-full w-full pointer-events-none text-white fill-current z-10" 
                 viewBox="0 0 500 600" preserveAspectRatio="none">
                <path d="M 0 0 L 260 0 C 260 110, 130 180, 75 320 C 35 410, 0 500, 0 600 Z" />
            </svg>

            {{-- LINGKARAN CAHAYA BULAN DI ATAS KANAN --}}
            <div class="absolute top-10 right-10 w-14 h-14 rounded-full bg-gradient-to-tr from-yellow-300 to-amber-100 opacity-90 blur-[1px] shadow-[0_0_25px_rgba(251,191,36,0.7)]"></div>

            {{-- TEKS DESKRIPSI KANAN BISA DITAMPILKAN SECARA ELEGAT --}}
            <div class="absolute inset-0 flex flex-col justify-end p-12 text-white z-20">
                <div class="max-w-xs space-y-2 mb-2">
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold tracking-widest text-amber-300 uppercase border border-white/10">
                        Sistem Pengaduan
                    </span>
                    <h3 class="text-xl font-bold tracking-tight drop-shadow-md">RSUD dr. H. Moh. Anwar</h3>
                    <p class="text-xs text-purple-200/80 leading-relaxed">
                        Layanan pengaduan integratif, cepat, dan terpercaya bagi seluruh pengguna SIM RSUD.
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- 4. FOOTER LUAR CARD (SESUAI DENGAN POSISI TEKS BAWAH GAMBAR ACUAN) --}}
    <div class="absolute bottom-4 left-6 lg:left-16 text-xs text-[#b0a5d1] pointer-events-none z-0">
        <p>&copy; {{ date('Y') }} IPP-RSUD RSUD dr. H. Moh. Anwar Sumenep</p>
    </div>

</body>
</html>