<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIM RSUD</title>
    
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #F6F5FB;
        }
        /* Menghilangkan background autofill bawaan browser agar tetap rapi */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: #2D235C !important;
        }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex items-center justify-center p-4 sm:p-8 relative overflow-hidden">
    
    <!-- Elemen Dekorasi Background Luar (Meniru background presentasi pada gambar) -->
    <div class="absolute top-8 left-8 md:top-12 md:left-12 -z-10 pointer-events-none opacity-20">
        <h2 class="text-3xl font-semibold text-[#8B86A9] mb-1">Welcome to</h2>
        <h1 class="text-7xl font-bold text-[#8B86A9] tracking-tight">inSpace</h1>
    </div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10 transform translate-x-1/3 translate-y-1/3"></div>

    <!-- MAIN CARD CONTAINER -->
    <div class="w-full max-w-5xl bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.08)] flex flex-col md:flex-row relative z-10 min-h-[550px]">
        
        <!-- BAGIAN KIRI: FORM LOGIN -->
        <div class="w-full md:w-[45%] bg-white p-10 lg:p-16 flex flex-col justify-center relative z-20 rounded-l-[2rem]">
            
            <!-- Titik Oranye -->
            <div class="w-5 h-5 bg-[#F2A43B] rounded-full mb-8"></div>

            <!-- Judul -->
            <div class="mb-10">
                <h2 class="text-sm font-semibold text-[#8B86A9] tracking-wide mb-1">Welcome to</h2>
                <h1 class="text-3xl font-bold text-[#2D235C] tracking-tight">SIM RSUD</h1>
            </div>

            @if(session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="w-full">
                @csrf

                <!-- Input Email -->
                <div class="mb-6 relative">
                    <label for="email" class="block text-xs font-bold text-[#8B86A9] mb-1">Email</label>
                    <input id="email" 
                           class="block w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-[#B352D0] bg-transparent px-0 py-2 text-sm text-[#2D235C] font-semibold transition-colors" 
                           type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                           placeholder="petugas@rsud.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
                </div>

                <!-- Input Password -->
                <div class="mb-8 relative">
                    <label for="password" class="block text-xs font-bold text-[#8B86A9] mb-1 mt-4">Password</label>
                    <input id="password" 
                           class="block w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-[#B352D0] bg-transparent px-0 py-2 text-2xl tracking-widest text-[#2D235C] font-semibold transition-colors placeholder:text-xl placeholder:tracking-widest placeholder:text-gray-400"
                           type="password" name="password" required autocomplete="current-password" 
                           placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
                </div>

                <!-- Tombol Login -->
                <div>
                    <button type="submit" class="bg-[#B352D0] hover:bg-[#9E45B8] text-white font-semibold py-2.5 px-10 rounded-full shadow-[0_8px_15px_rgba(179,82,208,0.3)] transform hover:-translate-y-0.5 transition-all duration-200 text-xs tracking-wide">
                        LOGIN
                    </button>
                </div>
            </form>

            <!-- Link Footer (Mengikuti teks persis di gambar) -->
            <div class="mt-12 text-xs text-[#8B86A9] font-medium">
                Dont have an account? <a href="#" class="text-[#B352D0] font-semibold hover:underline">Sign up</a>
            </div>
        </div>

        <!-- BAGIAN KANAN: GAMBAR & KURVA PEMISAH -->
        <div class="w-full md:w-[55%] relative hidden md:block overflow-hidden rounded-r-[2rem] bg-[#2A155C]">
            
            <!-- SVG Kurva (Wave) Pemisah berwarna putih -->
            <svg class="absolute top-0 -left-[1px] h-full w-24 lg:w-40 text-white z-10" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0,0 C100,0 100,30 100,100 L0,100 Z"></path>
            </svg>

            <!-- 
              GANTI URL GAMBAR DI BAWAH INI DENGAN GAMBAR VEKTOR ANDA.
              Saya menggunakan placeholder ilustrasi malam agar sesuai tema, 
              namun Anda bisa mengubahnya ke {{ asset('images/nama-file-gambar-anda.jpg') }}
            -->
            <div class="absolute inset-0 bg-cover bg-left-bottom bg-no-repeat transition-transform duration-1000" 
                 style="background-image: url('{{ asset('images/ilustrasi-malam.jpg') }}');">
                 <!-- Jika Anda tidak memiliki gambarnya, biarkan kosong dan area ini akan berwarna gradient ungu yang menyerupai tema gambar referensi -->
            </div>
            
            <!-- Efek Warna Opsional: Memastikan nuansa ungu malam hari selalu ada -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#401F82] via-[#7D34A7] to-[#B352D0] mix-blend-overlay opacity-80 pointer-events-none"></div>
            
        </div>
    </div>
</body>
</html>