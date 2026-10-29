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
<body class="relative min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 antialiased overflow-x-hidden bg-slate-950">

    {{-- FULLSCREEN BACKGROUND IMAGE (GEDUNG RSUD SEBAGAI BASE BACKGROUND LAYER) --}}
    <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat scale-105 transform transition-transform duration-1000"
         style="background-image: url('{{ asset('images/gedung-rsud.png') }}');">
    </div>
    
    {{-- GRADIENT OVERLAY & BLUR UNTUK EFEK KEDALAMAN KANVAS (3D DEPTH) --}}
    <div class="fixed inset-0 z-0 bg-gradient-to-br from-[#12072b]/90 via-[#2a1052]/80 to-[#080214]/95 backdrop-blur-[3px]"></div>

    {{-- GLOWING AMBIENT LIGHTS (CAHAYA DEKORATIF UNTUK EFEK POP-OUT 3D) --}}
    <div class="fixed -top-24 -left-24 w-[30rem] h-[30rem] bg-purple-600/30 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed -bottom-24 -right-24 w-[35rem] h-[35rem] bg-indigo-500/25 rounded-full blur-[140px] pointer-events-none z-0"></div>

    {{-- MAIN CONTAINER CARD (CARD MELAYANG DENGAN SHADOW KEBENAMAN DEEP 3D) --}}
    <div class="relative z-10 w-full max-w-5xl bg-white/95 backdrop-blur-2xl rounded-3xl shadow-[0_35px_60px_-15px_rgba(0,0,0,0.6)] border border-white/20 overflow-hidden flex flex-col lg:flex-row min-h-[580px]">
        
        {{-- BAGIAN KIRI: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-between z-20 bg-white">
            <div>
                {{-- LOGO DOT KUNING/ORANGE --}}
                <div class="mb-8">
                    <div class="w-6 h-6 rounded-full bg-[#f3a638] shadow-md shadow-orange-500/30"></div>
                </div>

                {{-- JUDUL HALAMAN --}}
                <div class="mb-8">
                    <p class="text-sm font-semibold text-[#a198c3] tracking-wide">Selamat Datang di</p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#221543] tracking-tight mt-0.5">
                        SIM RSUD
                    </h1>
                    <p class="text-xs text-[#a198c3] mt-1 font-medium">RSUD dr. H. Moh. Anwar Sumenep</p>
                </div>

                @if(session('status'))
                    <div class="mb-5 p-3 rounded-lg bg-green-50 text-xs font-semibold text-green-600 border border-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- FORM --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- EMAIL FIELD (UNDERLINE STYLE) --}}
                    <div class="relative">
                        <label for="email" class="block text-xs font-semibold text-[#a198c3] uppercase tracking-wider mb-1">
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
                               class="w-full bg-transparent border-b border-[#e1dbe8] focus:border-[#a251d1] text-[#221543] text-sm py-2 px-0 outline-none transition-colors duration-200 placeholder-[#c5bedb]" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    {{-- PASSWORD FIELD (UNDERLINE STYLE) --}}
                    <div class="relative">
                        <label for="password" class="block text-xs font-semibold text-[#a198c3] uppercase tracking-wider mb-1">
                            Kata Sandi
                        </label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••" 
                               class="w-full bg-transparent border-b border-[#e1dbe8] focus:border-[#a251d1] text-[#221543] text-sm py-2 px-0 outline-none transition-colors duration-200 placeholder-[#c5bedb]" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer text-[#a198c3] hover:text-[#221543]">
                            <input id="remember_me" type="checkbox" class="rounded border-[#e1dbe8] text-[#a251d1] focus:ring-[#a251d1]" name="remember">
                            <span class="ms-2 font-medium">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    {{-- SUBMIT BUTTON (PILL / CAPSULE SHAPE) --}}
                    <div class="pt-2">
                        <button type="submit" 
                                class="bg-[#aa4bce] hover:bg-[#953cb7] active:scale-[0.98] text-white text-xs font-bold uppercase tracking-wider py-3 px-10 rounded-full shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 transition-all duration-200">
                            {{ __('LOGIN') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- FOOTER / LINK --}}
            <div class="mt-8 text-xs text-[#a198c3]">
                <span>Lupa kata sandi atau butuh akses?</span>
                <a href="#" class="font-bold text-[#aa4bce] hover:underline ms-1">Hubungi Admin</a>
                <p class="text-[10px] text-gray-400 mt-4">&copy; {{ date('Y') }} IPP-RSUD RSUD dr. H. Moh. Anwar Sumenep</p>
            </div>
        </div>

        {{-- BAGIAN KANAN: PANEL DENGAN GAMBAR GEDUNG & CURVE YANG BERSATU DENGAN OUTDOOR BACKGROUND --}}
        <div class="hidden lg:block lg:w-1/2 relative bg-cover bg-center overflow-hidden"
             style="background-image: url('{{ asset('images/gedung-rsud.png') }}');">
            
            {{-- OVERLAY DENGAN GRADIENT SERUPA UNTUK CONTINUITY LAYERING --}}
            <div class="absolute inset-0 bg-gradient-to-br from-[#3b1578]/80 via-[#5b1f9b]/75 to-[#1c0a3d]/90 backdrop-blur-[1px]"></div>

            {{-- SVG CURVED MASK --}}
            <svg class="absolute top-0 left-0 h-full w-full pointer-events-none text-white fill-current z-10" 
                 viewBox="0 0 500 600" preserveAspectRatio="none">
                <path d="M 0 0 L 260 0 C 260 110, 140 230, 80 340 C 30 420, 0 510, 0 600 Z" />
            </svg>

            {{-- ELEMENT DEKORATIF GLOWING MOON & TEKS KANAN --}}
            <div class="absolute top-10 right-10 w-16 h-16 rounded-full bg-gradient-to-tr from-yellow-300 to-amber-100 opacity-90 blur-[1px] shadow-[0_0_30px_rgba(251,191,36,0.8)]"></div>

            <div class="absolute inset-0 flex flex-col justify-end p-12 text-white z-20">
                <div class="max-w-xs space-y-2 mb-4">
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[11px] font-semibold tracking-wider text-amber-300 uppercase border border-white/10 shadow-sm">
                        Sistem Pengaduan
                    </span>
                    <h2 class="text-2xl font-bold tracking-tight drop-shadow-lg">Layanan Pengaduan Integratif</h2>
                    <p class="text-xs text-purple-200/90 leading-relaxed drop-shadow">
                        Memberikan pelayanan yang transparan, cepat, dan terpercaya bagi seluruh pengguna SIM RSUD Sumenep.
                    </p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>