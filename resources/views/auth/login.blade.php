<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIM RSUD Sumenep</title>
    
    <script src="{{ asset('js/tailwind.js') }}"></script>

    {{-- Tambahan Style agar font tetap rapi walau offline --}}
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50 selection:bg-blue-200 selection:text-blue-900">
    
    <div class="min-h-screen flex w-full">
        
        {{-- BAGIAN KIRI: GAMBAR GEDUNG & INFORMASI (Desain Modern) --}}
        <div class="hidden lg:flex w-1/2 bg-cover bg-center relative items-center justify-center overflow-hidden" 
             style="background-image: url('{{ asset('images/gedung-rsud.jpg') }}');">
            
            <!-- Overlay Gradient Biru Premium -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/95 via-blue-800/80 to-blue-900/95 z-0"></div>

            <!-- Konten Bagian Kiri -->
            <div class="relative z-10 w-full px-12 flex flex-col items-center justify-center text-center text-white h-full">
                
                <!-- Ikon / Logo Instansi -->
                <div class="mb-6 p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20 shadow-lg">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>

                <h1 class="text-4xl lg:text-5xl font-extrabold mb-4 tracking-tight drop-shadow-lg">
                    Sistem Instalasi<br><span class="text-blue-300">Pengaduan</span>
                </h1>
                <p class="text-lg font-light text-blue-100 max-w-md leading-relaxed mb-10">
                    Layanan pengaduan terpadu untuk peningkatan mutu dan pelayanan RSUD dr. H. Moh. Anwar Sumenep.
                </p>

                <!-- Status Sistem (Glassmorphism) -->
                <div class="mt-auto mb-8 bg-white/10 backdrop-blur-md border border-white/20 rounded-full py-2 px-5 flex items-center gap-3 shadow-lg">
                    <div class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></div>
                    <span class="text-sm font-medium tracking-wide">Sistem Terhubung & Aman</span>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 relative overflow-hidden p-6 sm:p-12">
            
            <!-- Elemen Dekoratif Latar Belakang -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-blue-200/40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 rounded-full bg-teal-200/40 blur-3xl"></div>

            <!-- Card Form Login -->
            <div class="w-full max-w-md bg-white/90 backdrop-blur-xl p-8 sm:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 relative z-10">
                
                <!-- Tampil hanya di Mobile -->
                <div class="lg:hidden flex justify-center mb-6">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 border border-blue-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">Selamat Datang 👋</h2>
                    <p class="text-gray-500 mt-2 text-sm font-medium">Masuk menggunakan kredensial petugas.</p>
                </div>

                @if(session('status'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Field Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Petugas</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                                   class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-200 shadow-sm sm:text-sm" 
                                   placeholder="admin@rsud.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-medium" />
                    </div>

                    <!-- Field Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password" 
                                   class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-200 shadow-sm sm:text-sm"
                                   placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs font-medium" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 transition-colors">
                            <span class="ms-2 text-sm text-gray-600 font-medium hover:text-gray-800 transition-colors">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="w-full relative group overflow-hidden bg-blue-600 text-white font-bold py-3.5 px-4 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 shadow-[0_5px_15px_rgba(37,99,235,0.3)] hover:shadow-[0_8px_25px_rgba(37,99,235,0.4)] hover:-translate-y-0.5">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            {{ __('MASUK APLIKASI') }}
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                        <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-blue-700 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0"></div>
                    </button>
                </form>

                <!-- Footer Copyright -->
                <div class="mt-10 pt-6 border-t border-gray-100/80 text-center">
                    <p class="text-xs text-gray-400 font-medium">
                        &copy; {{ date('Y') }} IPP-RSUD <br> RSUD dr. H. Moh. Anwar Sumenep
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>