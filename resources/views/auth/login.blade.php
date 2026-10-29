<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>inSpace Login</title>
    
    <!-- Menggunakan CDN Tailwind CSS untuk kemudahan -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Menggunakan font Nunito untuk menyamai tipografi yang membulat pada desain asli */
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Nunito', sans-serif; 
        }
        
        /* Menghilangkan efek outline bawaan browser pada input */
        input:focus { 
            outline: none; 
            box-shadow: none; 
        }
    </style>
</head>
<body class="bg-[#f0f1f7] min-h-screen flex items-center justify-center p-4 relative overflow-hidden text-[#2b225c]">
    
    <!-- Watermark Background Kiri Atas -->
    <div class="absolute top-10 left-12 opacity-30 pointer-events-none hidden lg:block select-none">
        <h2 class="text-[#898ba8] text-4xl font-bold mb-[-12px]">Welcome to</h2>
        <h1 class="text-[#898ba8] text-[5.5rem] font-extrabold tracking-tight">inSpace</h1>
    </div>

    <!-- Watermark Background Kiri Bawah -->
    <div class="absolute bottom-10 left-12 opacity-40 pointer-events-none hidden lg:block select-none">
        <p class="text-[#898ba8] text-xl font-medium">
            Dont have an account? <span class="font-bold border-b-2 border-[#898ba8] pb-1">Sign up</span>
        </p>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-white w-full max-w-[950px] h-[600px] rounded-[2rem] shadow-[0_25px_60px_rgba(0,0,0,0.08)] flex relative z-10 overflow-hidden">
        
        <!-- BAGIAN KIRI: Form Login -->
        <div class="w-full md:w-[42%] h-full p-12 lg:p-14 flex flex-col justify-center relative z-20">
            
            <!-- Ornamen Titik Oranye[cite: 2] -->
            <div class="w-5 h-5 rounded-full bg-[#f4a74d] mb-10 shadow-sm"></div>
            
            <!-- Judul -->
            <div class="mb-12">
                <h2 class="text-[#8a85a4] font-bold text-[13px] tracking-wide mb-1">Welcome to</h2>
                <h1 class="text-[#2b225c] font-extrabold text-4xl tracking-tight">inSpace</h1>
            </div>

            <!-- Form -->
            <form action="#" method="POST" class="w-full">
                
                <!-- Input Email -->
                <div class="mb-6">
                    <label class="block text-[#8a85a4] font-bold text-[11px] mb-2">Email</label>
                    <input type="email" 
                           value="bonjourguys@talk.com" 
                           class="w-full border-0 border-b border-gray-300 pb-2 px-0 text-[13px] text-[#2b225c] font-bold bg-transparent focus:ring-0 focus:border-[#a855c3] transition-colors" />
                </div>

                <!-- Input Password -->
                <div class="mb-10">
                    <label class="block text-[#8a85a4] font-bold text-[11px] mb-2 mt-4">Password</label>
                    <input type="password" 
                           value="........" 
                           class="w-full border-0 border-b border-gray-300 pb-2 px-0 text-xl text-[#2b225c] font-black tracking-[0.4em] bg-transparent focus:ring-0 focus:border-[#a855c3] transition-colors" />
                </div>

                <!-- Tombol Login -->
                <button type="button" class="bg-[#ab58c5] hover:bg-[#974bb0] text-white font-bold text-[11px] tracking-wider py-3.5 px-10 rounded-full shadow-[0_8px_20px_rgba(171,88,197,0.4)] transition-all transform hover:-translate-y-0.5">
                    LOGIN
                </button>
            </form>

            <!-- Teks Footer Form[cite: 2] -->
            <div class="mt-auto pt-8">
                <p class="text-[#8a85a4] text-[11px] font-bold">
                    Dont have an account? 
                    <a href="#" class="text-[#ab58c5] hover:underline ml-1 relative">
                        Sign up
                        <!-- Garis bawah tipis khas pada desain -->
                        <span class="absolute left-0 -bottom-1 w-full h-[1px] bg-[#ab58c5] opacity-50"></span>
                    </a>
                </p>
            </div>
        </div>

        <!-- BAGIAN KANAN: Ilustrasi -->
        <!-- Penggunaan border-top-left-radius besar untuk menciptakan efek kurva bergelombang antara putih dan gambar secara natural[cite: 2] -->
        <div class="hidden md:block md:w-[58%] h-full relative bg-gradient-to-br from-[#4b27a8] via-[#7d39c9] to-[#3a1d7c]" 
             style="border-top-left-radius: 140px;">
             
            <!-- 
                PENTING: Untuk membuat bagian kanan 100% sama dengan gambar, 
                potong (crop) ilustrasi rumah dari gambar referensi Anda, simpan sebagai file (misal: 'illustration.png'), 
                dan masukkan URL/path gambarnya ke dalam style background-image di bawah ini.
            -->
            <div class="absolute inset-0 bg-cover bg-no-repeat bg-right-bottom" 
                 style="background-image: url('URL_GAMBAR_ILUSTRASI_ANDA_DISINI'); border-top-left-radius: 140px;">
            </div>
            
        </div>
    </div>
</body>
</html>