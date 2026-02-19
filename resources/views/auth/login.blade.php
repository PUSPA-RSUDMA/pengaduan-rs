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
<body class="font-sans text-gray-900 antialiased">
    
    <div class="min-h-screen flex">
        
        {{-- BAGIAN KIRI: GAMBAR GEDUNG --}}
        <div class="hidden lg:block w-1/2 bg-cover bg-center relative" 
             style="background-image: url('{{ asset('images/gedung-rsud.jpg') }}');">
            
            <div class="absolute inset-0 bg-blue-900 bg-opacity-70"></div>

            <div class="absolute inset-0 flex flex-col justify-center items-center text-white p-10 text-center z-10">
                <h1 class="text-4xl font-bold mb-2 drop-shadow-md">Sistem Pengaduan Terpadu</h1>
                <p class="text-xl font-medium tracking-wide drop-shadow-md">RSUD dr. H. Moh. Anwar Sumenep</p>
                <div class="w-20 h-1 bg-yellow-400 mt-4 rounded-full"></div>
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM LOGIN --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 p-8">
            <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
                
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Selamat Datang!</h2>
                    <p class="text-gray-500 mt-2 text-sm">Silakan masuk menggunakan akun petugas.</p>
                </div>

                @if(session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Petugas</label>
                        <input id="email" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" 
                               type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                               placeholder="admin@rsud.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        <input id="password" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                                type="password"
                                name="password"
                                required autocomplete="current-password" 
                                placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex items-center mb-6">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-200 shadow-lg transform hover:-translate-y-0.5">
                        {{ __('MASUK APLIKASI') }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} SIM-RSUD v1.0 • Built for RSUD dr. H. Moh. Anwar Sumenep</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>