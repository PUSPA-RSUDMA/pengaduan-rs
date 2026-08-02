<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPP-RSUD Admin</title>
    {{-- CDN Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Google: Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f4f7f6; /* Warna background lebih soft */
            overflow-x: hidden; 
        }
        
        /* === SIDEBAR MODERN === */
        #sidebar-wrapper {
            width: 270px;
            height: 100vh;
            /* Gradient Background yang lebih elegan */
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        /* Custom Scrollbar untuk Sidebar */
        #sidebar-wrapper::-webkit-scrollbar { width: 6px; }
        #sidebar-wrapper::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        #sidebar-wrapper::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        #sidebar-wrapper::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }

        /* === KONTEN UTAMA === */
        #page-content-wrapper {
            width: calc(100% - 270px);
            margin-left: 270px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* === HEADER / NAVBAR MODERN === */
        .main-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 12px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid #edf2f9;
        }

        /* === LOGIKA TOGGLE === */
        body.toggled #sidebar-wrapper { margin-left: -270px; }
        body.toggled #page-content-wrapper { width: 100%; margin-left: 0; }

        /* === RESPONSIVE (MOBILE) === */
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -270px; }
            #page-content-wrapper { width: 100%; margin-left: 0; }
            body.toggled #sidebar-wrapper { margin-left: 0; }
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(3px); z-index: 998; top:0; left:0; transition: 0.3s; }
            body.toggled .overlay { display: block; }
        }

        /* === STYLING MENU ITEM === */
        .sidebar-header { 
            padding: 25px 20px; 
            border-bottom: 1px solid rgba(255,255,255,0.08); 
        }
        .sidebar-label { 
            padding: 20px 25px 10px; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            color: #64748b; 
            font-weight: 700; 
        }
        
        .list-group-item { 
            background: transparent; 
            color: #94a3b8; 
            border: none; 
            padding: 10px 20px; 
            margin: 4px 15px; /* Margin agar melengkung */
            border-radius: 8px; /* Sudut melengkung */
            transition: all 0.3s ease; 
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .list-group-item i { font-size: 1.1rem; transition: 0.3s; }

        /* Efek Hover & Active */
        .list-group-item:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: #f8fafc; 
            transform: translateX(5px); /* Efek geser kanan sedikit */
        }
        .list-group-item.active { 
            background: #3b82f6; /* Biru modern */
            color: #ffffff; 
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .list-group-item.active i { color: #ffffff !important; }

        /* === ANIMASI LONCENG === */
        @keyframes ring {
            0% { transform: rotate(0); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(15deg); }
            40% { transform: rotate(-10deg); }
            50% { transform: rotate(0); }
            100% { transform: rotate(0); }
        }
        .bell-ring {
            animation: ring 2s ease infinite;
            display: inline-block;
            transform-origin: top center;
            color: #ef4444; /* Merah menyala */
        }
        
        /* Dropdown Notifikasi */
        .notif-dropdown { width: 320px; border-radius: 12px; }
        .notif-item { transition: 0.2s; border-radius: 8px; margin: 4px 8px; }
        .notif-item:hover { background-color: #f8fafc; }
    </style>
</head>
<body>

    {{-- MENGAMBIL DATA LOGBOOK UNTUK BESOK SECARA OTOMATIS --}}
    @php
        $besok = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        // Pastikan Model Logbook di-import dengan benar
        $logbookNotifs = \App\Models\Logbook::where('tanggal_acara', $besok)->get();
        $notifCount = $logbookNotifs->count();
    @endphp
    
    <div class="overlay" id="overlayClick"></div>

    <div id="wrapper">
        
        {{-- SIDEBAR --}}
        <nav id="sidebar-wrapper">
            <div class="sidebar-header d-flex align-items-center">
                <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center me-3 shadow" style="width: 45px; height: 45px;">
                    <i class="bi bi-hospital-fill text-white fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5 text-white tracking-wide">IPP-RSUD</div>
                    <small class="text-white-50" style="font-size: 0.75rem;">dr. H. Moh. Anwar</small>
                </div>
            </div>
            
            <div class="list-group list-group-flush pb-4 mt-2">
                <div class="sidebar-label">Menu Utama</div>
                
                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill me-3 text-primary"></i> Dashboard
                    </a>
                @endif
                
                <a href="{{ route('complaints.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text-fill me-3 text-info"></i> Data Keluhan
                </a>

                <div class="sidebar-label mt-2">Layanan & Informasi</div>

                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('dashboard.permintaan') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard.permintaan') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart-fill me-3 text-warning"></i> Dashboard Layanan
                    </a>
                @endif

                <a href="{{ route('permintaan.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('permintaan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-medical-fill me-3 text-success"></i> Data Permintaan
                </a>
                
                <div class="sidebar-label mt-2">Layanan Mobil Lasehat</div>
                
                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('lasehat.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('lasehat.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-3 text-danger"></i> Dashboard LaSehat
                    </a>
                @endif

                <a href="{{ route('lasehat.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('lasehat.index') ? 'active' : '' }}">
                    <i class="bi bi-car-front-fill me-3 text-danger"></i> Data LaSehat
                </a>

                <div class="sidebar-label mt-2">Manajemen Agenda</div>
                <a href="{{ route('logbook.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('logbook.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-calendar-event-fill me-3 text-primary"></i> Logbook Agenda</div>
                    @if($notifCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span>
                    @endif
                </a>
                <a href="{{ route('whatsapp.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
                    <i class="bi bi-whatsapp me-3 text-success"></i> Koneksi WhatsApp
                </a>

                @if(auth()->user()->role == 'admin')
                    <div class="sidebar-label mt-3">Data Master</div>
                    
                    <a href="{{ route('master.units.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.units.*') ? 'active' : '' }}">
                        <i class="bi bi-building-fill me-3 text-secondary"></i> Unit Tujuan
                    </a>
                    
                    <a href="{{ route('sources.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('sources.*') ? 'active' : '' }}">
                        <i class="bi bi-hdd-network-fill me-3 text-secondary"></i> Media Pengaduan
                    </a>

                    <a href="{{ route('master.reporters.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.reporters.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill me-3 text-secondary"></i> Unit Pelapor
                    </a>

                    <a href="{{ route('kategori-keluhan.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('kategori-keluhan.*') ? 'active' : '' }}">
                        <i class="bi bi-ui-checks-grid me-3 text-secondary"></i> Kategori Keluhan
                    </a>

                    <a href="{{ route('master.supir.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.supir.*') ? 'active' : '' }}">
                        <i class="bi bi-person-vcard-fill me-3 text-secondary"></i> Master Supir
                    </a>
                    
                    <a href="{{ route('master.grades.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.grades.*') ? 'active' : '' }}">
                        <i class="bi bi-flag-fill me-3 text-secondary"></i> Kegawatan
                    </a>
                    
                    <div class="sidebar-label mt-3">Sistem</div>
                    <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-3 text-light"></i> Kelola User
                    </a>
                @endif
                
                {{-- Tombol Logout Mobile --}}
                <div class="mt-4 px-3 d-md-none">
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 btn-sm rounded-pill shadow-sm">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- KONTEN HALAMAN --}}
        <div id="page-content-wrapper">
            
            {{-- HEADER --}}
            <nav class="main-header">
                {{-- KIRI: Tombol Toggle & Judul --}}
                <div class="d-flex align-items-center">
                    <button class="btn btn-white border-0 shadow-sm me-3 rounded-circle d-flex align-items-center justify-content-center" id="menu-toggle" style="width: 40px; height: 40px;">
                        <i class="bi bi-list fs-5 text-dark"></i>
                    </button>
                    <h5 class="fw-bold m-0 text-dark">
                        @yield('title', 'Dashboard Panel')
                    </h5>
                </div>
                
                {{-- KANAN: Lonceng Notif & Profil --}}
                <div class="d-flex align-items-center">
                    
                    {{-- DROPDOWN NOTIFIKASI LOGBOOK --}}
                    <div class="dropdown me-3">
                        <a href="#" class="text-dark position-relative d-flex align-items-center justify-content-center bg-light rounded-circle border" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; text-decoration: none;">
                            <i class="bi bi-bell-fill fs-5 text-secondary {{ $notifCount > 0 ? 'bell-ring' : '' }}"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow" style="font-size: 0.65rem;">
                                    {{ $notifCount }}
                                    <span class="visually-hidden">notifikasi belum dibaca</span>
                                </span>
                            @endif
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 notif-dropdown pb-0" aria-labelledby="notifDropdown">
                            <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light" style="border-radius: 12px 12px 0 0;">
                                <span class="fw-bold mb-0">Pengingat Logbook</span>
                                <span class="badge bg-danger rounded-pill">{{ $notifCount }} H-1</span>
                            </li>
                            <div style="max-height: 280px; overflow-y: auto;" class="py-2">
                                @forelse($logbookNotifs as $notif)
                                <li>
                                    <a class="dropdown-item notif-item d-flex align-items-start py-2" href="{{ route('logbook.index') }}">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3 mt-1" style="width: 35px; height: 35px; flex-shrink: 0;">
                                            <i class="bi bi-calendar-check-fill"></i>
                                        </div>
                                        <div style="overflow: hidden;">
                                            <div class="fw-semibold text-truncate text-dark" style="font-size: 0.85rem;">{{ $notif->judul_acara }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ $notif->deskripsi ?? 'Tidak ada deskripsi' }}</div>
                                            <small class="text-danger fw-bold" style="font-size: 0.7rem;">Besok - {{ \Carbon\Carbon::parse($notif->tanggal_acara)->format('d M Y') }}</small>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="p-4 text-center">
                                    <i class="bi bi-check2-circle text-success fs-1 mb-2 d-block"></i>
                                    <small class="text-muted fw-medium">Yeay! Tidak ada agenda H-1 untuk besok.</small>
                                </li>
                                @endforelse
                            </div>
                            <li class="border-top">
                                <a href="{{ route('logbook.index') }}" class="dropdown-item text-center text-primary fw-bold py-2" style="font-size: 0.85rem; border-radius: 0 0 12px 12px;">
                                    Buka Kalender Logbook <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- DROPDOWN PROFIL --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark bg-white px-3 py-2 rounded-pill border shadow-sm" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0ea5e9&color=fff&bold=true" class="rounded-circle me-2" width="32" height="32">
                            <div class="d-none d-md-block text-start me-1" style="line-height: 1.2;">
                                <div class="small fw-bold">{{ Auth::user()->name }}</div>
                                <div style="font-size: 0.65rem;" class="text-muted text-uppercase fw-semibold">{{ Auth::user()->role }}</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3" style="border-radius: 12px;" aria-labelledby="dropdownUser1">
                            <li><h6 class="dropdown-header text-primary fw-bold">Halo, {{ Auth::user()->name }}!</h6></li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear me-2 text-secondary"></i> Profil & Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            {{-- ISI KONTEN --}}
            <div class="container-fluid p-4">
                @yield('content')
            </div>

            {{-- FOOTER --}}
            <footer class="bg-white text-center py-3 mt-auto border-top">
                <div class="container">
                    <small class="text-muted fw-medium">Copyright &copy; {{ date('Y') }} RSUD dr. H. Moh. Anwar Sumenep</small>
                    <br>
                    <small class="text-muted" style="font-size: 0.75rem;">Developed by <span class="fw-bold text-primary">Vinda Sartika Basri</span></small>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- SCRIPT TOGGLE --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toggleBtn = document.getElementById("menu-toggle");
            var overlay = document.getElementById("overlayClick");

            function toggleMenu(e) {
                e.preventDefault();
                document.body.classList.toggle("toggled");
            }

            if(toggleBtn) toggleBtn.addEventListener("click", toggleMenu);
            if(overlay) overlay.addEventListener("click", toggleMenu);
        });
    </script>
</body>
</html>