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
        /* Style khusus untuk input agar saat autofill background tetap transparan */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: black !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="antialiased min-h-screen relative flex items-center justify-center bg-gray-900 bg-cover bg-center bg-fixed" 
      style="background-image: url('{{ asset('images/gedung-rsud.png') }}');">
    
    <!-- Overlay gelap untuk background utama -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm z-0"></div>

    <!-- Main Container Layout (Mengikuti gaya panel putih besar dari referensi) -->
    <div class="relative z-10 w-full max-w-6xl p-4 flex items-center justify-center">
        
        <!-- White Card Container -->
        <div class="bg-white rounded-[3rem] p-3 lg:p-4 flex flex-col lg:flex-row w-full shadow-2xl relative overflow-hidden min-h-[650px]">
            
            <!-- BAGIAN KIRI: Gambar Card -->
            <div class="w-full lg:w-[45%] rounded-[2.5rem] relative overflow-hidden shadow-inner h-[400px] lg:h-auto">
                
                <!-- Gambar background untuk card kiri -->
                <img src="{{ asset('images/gedung-rsud.png') }}" class="absolute inset-0 w-full h-full object-cover z-0" alt="Gedung RSUD">
                
                <!-- Overlay gradien untuk menggelapkan gambar agar teks kuning terbaca -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30 z-10"></div>
                
                <!-- Badge Atas Kiri (Meniru gaya "JOIN WITH 30K+ USERS") -->
                <div class="absolute top-6 left-6 z-20 flex items-center gap-3">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-blue-500 overflow-hidden"><img src="https://ui-avatars.com/api/?name=A&background=random" alt="A"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-green-500 overflow-hidden"><img src="https://ui-avatars.com/api/?name=B&background=random" alt="B"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-[#dcfc43] flex items-center justify-center text-black text-xs font-bold z-10">IPP</div>
                    </div>
                    <div class="text-white">
                        <p class="text-xs font-bold tracking-widest uppercase">Sistem Pengaduan</p>
                        <p class="text-[10px] text-gray-300">RSUD dr. H. Moh. Anwar</p>
                    </div>
                </div>

                <!-- Teks Utama (Kiri Bawah Card, meniru "Back to nature.") -->
                <div class="absolute bottom-10 left-8 z-20">
                    <h1 class="text-[4rem] lg:text-[5.5rem] leading-[0.85] font-black text-[#dcfc43] tracking-tighter mb-4">
                        Pelayanan<br>Hebat.
                    </h1>
                    <p class="text-white text-sm font-medium tracking-wide">
                        Kelola laporan publik dengan mudah dan cepat.
                    </p>
                </div>
            </div>

            <!-- BAGIAN KANAN: Form Login -->
            <div class="w-full lg:w-[55%] p-8 lg:p-16 flex flex-col justify-center relative bg-white rounded-r-[3rem]">
                
                <!-- Ornamen opsional (menggantikan bentuk blob yang rumit) -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#f8fae8] rounded-full blur-3xl opacity-60 -mr-20 -mt-20 z-0 pointer-events-none"></div>

                <div class="relative z-10 w-full max-w-sm mx-auto">
                    
                    <!-- Logo / Judul -->
                    <div class="flex justify-center mb-8">
                        <div class="flex items-center gap-2 font-black text-2xl tracking-tighter">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            SIM RSUD
                        </div>
                    </div>

                    <!-- Teks Sambutan -->
                    <div class="text-center mb-10">
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Selamat datang kembali</h2>
                        <p class="text-gray-500 text-sm">Silakan masukkan detail akun petugas Anda</p>
                    </div>

                    @if(session('status'))
                        <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-xl text-center">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Field Email (Gaya Underline) -->
                        <div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                                   class="block w-full px-0 py-3 bg-transparent border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-black text-gray-900 placeholder-gray-400 transition-colors" 
                                   placeholder="Email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
                        </div>

                        <!-- Field Password (Gaya Underline) -->
                        <div>
                            <input id="password" type="password" name="password" required autocomplete="current-password" 
                                   class="block w-full px-0 py-3 bg-transparent border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-black text-gray-900 placeholder-gray-400 transition-colors"
                                   placeholder="Kata Sandi" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black cursor-pointer">
                                <span class="text-sm text-gray-700 font-medium">Ingat saya</span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-gray-900 hover:underline">Lupa Sandi?</a>
                            @endif
                        </div>

                        <!-- Tombol Login (Gaya Pill Panjang dengan Lingkaran Kuning) -->
                        <button type="submit" class="w-full relative group mt-6 bg-[#1a1a1a] rounded-full p-2 pl-6 flex items-center justify-between overflow-hidden shadow-lg hover:bg-black transition-colors duration-300">
                            <!-- Background Image Gelap Opsional -->
                            <div class="absolute inset-0 bg-[url('{{ asset('images/gedung-rsud.png') }}')] bg-cover bg-center opacity-20 mix-blend-overlay"></div>
                            
                            <span class="relative z-10 text-white font-semibold text-sm">Masuk Aplikasi</span>
                            
                            <!-- Lingkaran Kuning dengan Panah -->
                            <div class="relative z-10 bg-[#dcfc43] w-12 h-12 rounded-full flex items-center justify-center text-black group-hover:scale-95 transition-transform duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                                </svg>
                            </div>
                        </button>
                    </form>

                    <!-- Divider / Optional Button -->
                    <div class="mt-8 text-center text-sm text-gray-600 font-medium">
                        Bukan petugas? <a href="#" class="text-[#849c0c] hover:underline font-bold">Kunjungi Beranda</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>