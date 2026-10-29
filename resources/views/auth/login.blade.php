<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIM RSUD Sumenep</title>
    
    <script src="{{ asset('js/tailwind.js') }}"></script>

    <style>
        /* Mengatur font default agar terlihat rapi dan elegan */
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
        }
        /* Menambahkan font serif untuk judul agar mirip dengan referensi gambar */
        .font-elegant { font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif; }
    </style>
</head>
<body class="antialiased min-h-screen relative flex items-center justify-center bg-gray-900 bg-cover bg-center bg-fixed" 
      style="background-image: url('{{ asset('images/gedung-rsud.png') }}');">
    
    <!-- Overlay gelap & blur pada background utama agar card lebih menonjol -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm z-0"></div>

    <!-- Top Header Navigation (Seperti pada referensi) -->
    <div class="absolute top-0 left-0 w-full px-8 py-6 flex justify-between items-center z-20 text-white">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm border border-white/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="text-xl font-semibold tracking-wide">SIM RSUD</span>
        </div>
        <div class="hidden md:block text-xs font-semibold tracking-[0.2em] uppercase text-gray-300">
            Pelayanan Cepat &nbsp;/&nbsp; RSUD Sumenep
        </div>
    </div>

    <!-- Main Container Layout -->
    <div class="relative z-10 w-full max-w-6xl p-4 flex flex-col lg:flex-row gap-6 h-auto lg:h-[80vh] min-h-[600px] items-stretch">
        
        <!-- BAGIAN KIRI: Gambar Card -->
        <div class="hidden lg:flex w-1/2 rounded-[2rem] p-10 flex-col justify-between relative overflow-hidden shadow-2xl border border-white/20">
            
            <!-- Gambar background untuk card kiri -->
            <img src="{{ asset('images/gedung-rsud.png') }}" class="absolute inset-0 w-full h-full object-cover z-0" alt="Gedung RSUD">
            
            <!-- Overlay gradien hitam dari bawah ke atas agar teks terbaca -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/30 to-transparent z-10"></div>
            
            <!-- Badge Testimoni / Pengunjung (Kiri Atas Card) -->
            <div class="relative z-20 flex items-center gap-3 bg-white/20 backdrop-blur-md self-start py-2 px-4 rounded-full border border-white/30 shadow-sm">
                <div class="flex -space-x-2">
                    <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs font-bold">R</div>
                    <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs font-bold">S</div>
                    <div class="w-8 h-8 rounded-full bg-yellow-500 border-2 border-white flex items-center justify-center text-white text-xs font-bold">U</div>
                </div>
                <div class="text-white text-sm leading-tight">
                    <span class="font-bold">Sistem</span> Pengaduan<br>
                    <span class="text-xs text-gray-200">terintegrasi penuh...</span>
                </div>
            </div>

            <!-- Teks Utama (Kiri Bawah Card) -->
            <div class="relative z-20 mt-auto">
                <h1 class="text-white text-5xl leading-tight mb-4 tracking-tight">
                    <span class="font-elegant font-light">Pelayanan Hebat,</span><br>
                    <span class="font-semibold text-green-300 font-elegant">Dibuat Mudah.</span>
                </h1>
                <p class="text-gray-200 text-lg font-light max-w-md">
                    Kelola laporan dan pengaduan layanan RSUD dr. H. Moh. Anwar Sumenep untuk pelayanan publik yang lebih transparan.
                </p>
            </div>
        </div>

        <!-- BAGIAN KANAN: Form Login Card -->
        <div class="w-full lg:w-1/2 bg-white rounded-[2rem] p-8 lg:p-14 shadow-2xl relative overflow-hidden flex flex-col justify-center">
            
            <!-- Aksen Blob Latar Belakang (Seperti di gambar referensi) -->
            <div class="absolute top-0 right-0 w-80 h-80 bg-[#eef6f4] rounded-full blur-3xl opacity-70 -mr-20 -mt-20 z-0 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#eef6f4] rounded-full blur-3xl opacity-70 -ml-20 -mb-20 z-0 pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-md mx-auto">
                
                <!-- Icon Kecil (Opsional) -->
                <div class="w-10 h-10 bg-[#12372A] rounded-xl flex items-center justify-center mb-8 shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>

                <!-- Teks Sambutan -->
                <div class="mb-10">
                    <h2 class="text-3xl text-gray-900 mb-1">Selamat datang,</h2>
                    <h3 class="text-3xl text-[#12372A] font-elegant font-medium mb-3">Mari masuk ke akun Anda.</h3>
                    <p class="text-gray-500 text-sm">Log in untuk mulai mengelola sistem pengaduan.</p>
                </div>

                @if(session('status'))
                    <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-xl">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Field Email (Bentuk Pill/Rounded-Full) -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                               class="block w-full pl-12 pr-6 py-4 bg-[#f8faf9] border border-gray-200 rounded-full focus:bg-white focus:border-[#12372A] focus:ring-1 focus:ring-[#12372A] outline-none text-sm transition-all shadow-sm placeholder-gray-400" 
                               placeholder="Alamat Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 ml-4 text-red-500 text-xs" />
                    </div>

                    <!-- Field Password (Bentuk Pill/Rounded-Full) -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                               class="block w-full pl-12 pr-12 py-4 bg-[#f8faf9] border border-gray-200 rounded-full focus:bg-white focus:border-[#12372A] focus:ring-1 focus:ring-[#12372A] outline-none text-sm transition-all shadow-sm placeholder-gray-400"
                               placeholder="Kata Sandi" />
                        <!-- Eye Icon (Murni visual) -->
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center cursor-pointer">
                            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 ml-4 text-red-500 text-xs" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between pt-2 px-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#12372A] focus:ring-[#12372A] bg-gray-50 cursor-pointer">
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-900 hover:underline hover:text-[#12372A] transition-colors">Lupa sandi?</a>
                        @endif
                    </div>

                    <!-- Tombol Login (Pill-shaped, warna hijau gelap) -->
                    <button type="submit" class="w-full bg-[#12372A] hover:bg-[#0c261d] text-white font-medium py-4 px-6 rounded-full transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mt-6">
                        Log in
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>

            </div>
        </div>
    </div>
</body>
</html>