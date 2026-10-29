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
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
        }
        /* Custom input style to match the clean bottom-border look of the reference */
        input[type="email"], input[type="password"] {
            box-shadow: none !important;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-[#eceef6]">
    
    <!-- Outer wrapper with soft background -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative overflow-hidden">
        
        <!-- Faint background decorative elements (Optional: to match the soft presentation background) -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
        </div>

        <!-- MAIN CARD -->
        <div class="w-full max-w-5xl bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row relative z-10 min-h-[600px]">
            
            <!-- BAGIAN KIRI: FORM LOGIN (White Section) -->
            <div class="w-full md:w-5/12 bg-white p-10 lg:p-14 flex flex-col justify-center relative z-20">
                
                <!-- Orange Dot Decoration -->
                <div class="w-5 h-5 bg-[#f5a623] rounded-full mb-8 shadow-sm"></div>

                <!-- Titles -->
                <div class="mb-10">
                    <h2 class="text-sm font-semibold text-[#3b3575] tracking-wide mb-1">Welcome to</h2>
                    <h1 class="text-4xl font-extrabold text-[#241f55] tracking-tight">SIM RSUD</h1>
                </div>

                @if(session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="w-full">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-6">
                        <label for="email" class="block text-[11px] font-bold text-[#7a76a8] uppercase tracking-wider mb-1">Email</label>
                        <input id="email" 
                               class="block w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-[#b356f5] bg-transparent px-0 py-2 text-sm text-[#241f55] font-medium transition-colors" 
                               type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                               placeholder="admin@rsud.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-xs" />
                    </div>

                    <!-- Password Input -->
                    <div class="mb-6">
                        <label for="password" class="block text-[11px] font-bold text-[#7a76a8] uppercase tracking-wider mb-1 mt-4">Password</label>
                        <input id="password" 
                               class="block w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-[#b356f5] bg-transparent px-0 py-2 text-sm text-[#241f55] font-medium transition-colors"
                               type="password" name="password" required autocomplete="current-password" 
                               placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-xs" />
                    </div>

                    <!-- Remember Me (Styled subtly to fit the design) -->
                    <div class="flex items-center mb-10">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#b356f5] shadow-sm focus:ring-[#b356f5]" name="remember">
                            <span class="ms-2 text-xs text-[#7a76a8]">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="bg-gradient-to-r from-[#b356f5] to-[#9242ec] text-white font-bold py-3 px-12 rounded-full shadow-[0_8px_15px_rgba(179,86,245,0.3)] hover:shadow-[0_12px_20px_rgba(179,86,245,0.4)] transform hover:-translate-y-0.5 transition-all duration-200 text-xs tracking-widest uppercase">
                            {{ __('LOGIN') }}
                        </button>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="mt-16 text-[11px] text-[#a09cbf] font-medium">
                    <span class="mr-1">Dont have an account?</span>
                    <a href="#" class="text-[#b356f5] font-bold hover:underline">Sign up</a>
                    <p class="mt-3 text-gray-400">&copy; {{ date('Y') }} Sistem Instalasi Pengaduan RSUD dr. H. Moh. Anwar Sumenep</p>
                </div>
            </div>

            <!-- BAGIAN KANAN: GAMBAR & WAVE (Image Section) -->
            <div class="w-full md:w-7/12 relative hidden md:block overflow-hidden bg-[#241f55]">
                
                <!-- The custom S-curve Wave SVG to separate form and image smoothly -->
                <svg class="absolute left-[-1px] top-0 h-full w-24 lg:w-40 text-white z-10" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0,0 L100,0 C100,50 0,50 0,100 Z"></path>
                </svg>

                <!-- Background Image of Hospital -->
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 hover:scale-105" 
                     style="background-image: url('{{ asset('images/gedung-rsud.jpg') }}');">
                </div>
                
                <!-- Overlays to create the vibrant purple/night visual style from the reference -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#1a1154] via-[#46198f] to-[#b32b85] mix-blend-multiply opacity-95"></div>
                <div class="absolute inset-0 bg-[#2d1b70] mix-blend-overlay opacity-60"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#110c33] to-transparent opacity-80"></div>
                
            </div>
            
        </div>
    </div>
</body>
</html>