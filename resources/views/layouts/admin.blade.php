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
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        
        /* === SIDEBAR (FIXED) === */
        #sidebar-wrapper {
            width: 260px;
            height: 100vh;
            background: #2c3e50;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }

        /* === KONTEN UTAMA (KANAN) === */
        #page-content-wrapper {
            width: calc(100% - 260px); /* Lebar layar dikurangi lebar sidebar */
            margin-left: 260px; /* Geser ke kanan */
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* === HEADER / NAVBAR === */
        .main-header {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* === LOGIKA TOGGLE (BUKA TUTUP SIDEBAR) === */
        body.toggled #sidebar-wrapper {
            margin-left: -260px; /* Sembunyikan Sidebar */
        }
        
        body.toggled #page-content-wrapper {
            width: 100%; /* Konten jadi full layar */
            margin-left: 0;
        }

        /* === RESPONSIVE (MOBILE) === */
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -260px; } /* Default sembunyi */
            #page-content-wrapper { width: 100%; margin-left: 0; }

            /* Kalau ditoggle, sidebar muncul */
            body.toggled #sidebar-wrapper { margin-left: 0; box-shadow: 5px 0 15px rgba(0,0,0,0.3); }
            
            /* Overlay Gelap */
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 998; top:0; left:0; }
            body.toggled .overlay { display: block; }
        }

        /* Styling Menu */
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); background: #253545; }
        .list-group-item { background: transparent; color: #bdc3c7; border: none; padding: 12px 25px; transition: all 0.2s; border-left: 4px solid transparent; }
        .list-group-item:hover, .list-group-item.active { background: #34495e; color: #fff; border-left-color: #3498db; }
        .sidebar-label { padding: 25px 25px 10px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #7f8c8d; font-weight: 700; }
    </style>
</head>
<body>
    
    <div class="overlay" id="overlayClick"></div>

    <div id="wrapper">
        
        {{-- SIDEBAR --}}
        <nav id="sidebar-wrapper">
            <div class="sidebar-header d-flex align-items-center">
                <i class="bi bi-hospital-fill fs-3 me-3 text-info"></i>
                <div>
                    <div class="fw-bold fs-6">IPP-RSUD</div>
                    <small class="text-white-50" style="font-size: 0.7rem;">dr. H. Moh. Anwar</small>
                </div>
            </div>
            
            <div class="list-group list-group-flush pb-4">
                <div class="sidebar-label">Menu Utama</div>
                
                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill me-2"></i> Dashboard
                    </a>
                @endif
                
                <a href="{{ route('complaints.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                    <i class="bi bi-table me-2"></i> Data Keluhan
                </a>

                @if(auth()->user()->role == 'admin')
                    <div class="sidebar-label mt-2">Data Master</div>
                    
                    <a href="{{ route('master.units.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.units.*') ? 'active' : '' }}">
                        <i class="bi bi-hospital me-2"></i> Unit Tujuan
                    </a>
                    
                    <a href="{{ route('sources.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('sources.*') ? 'active' : '' }}">
                        <i class="bi bi-broadcast me-2"></i> Media Pengaduan
                    </a>

                    <a href="{{ route('master.reporters.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.reporters.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge me-2"></i> Unit Pelapor
                    </a>
                    
                    <a href="{{ route('master.grades.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('master.grades.*') ? 'active' : '' }}">
                        <i class="bi bi-flag-fill me-2"></i> Kegawatan
                    </a>
                    
                    <div class="sidebar-label mt-2">System</div>
                    <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Kelola User
                    </a>
                @endif
                
                {{-- Tombol Logout Mobile --}}
                <div class="mt-4 px-3 d-md-none">
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 btn-sm">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- KONTEN HALAMAN (Header + Isi + Footer) --}}
        <div id="page-content-wrapper">
            
            {{-- HEADER --}}
            <nav class="main-header">
                {{-- KIRI: Tombol & Judul --}}
                <div class="d-flex align-items-center">
                    <button class="btn btn-light border shadow-sm me-3" id="menu-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="fw-bold m-0 text-secondary">
                        @yield('title', 'Sistem Informasi')
                    </h5>
                </div>
                
                {{-- KANAN: Profil --}}
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark bg-light px-3 py-2 rounded-pill border" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=2c3e50&color=fff&bold=true" class="rounded-circle me-2" width="32" height="32">
                        <div class="d-none d-md-block text-start me-2" style="line-height: 1.2;">
                            <div class="small fw-bold">{{ Auth::user()->name }}</div>
                            <div style="font-size: 0.65rem;" class="text-muted text-uppercase">{{ Auth::user()->role }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUser1">
                        <li><h6 class="dropdown-header">Halo, {{ Auth::user()->name }}!</h6></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear me-2"></i> Profil & Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-bold">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            {{-- ISI KONTEN --}}
            <div class="container-fluid p-4">
                @yield('content')
            </div>

            {{-- FOOTER --}}
            <footer class="bg-white text-center py-3 border-top mt-auto">
                <div class="container">
                    <small class="text-muted fw-bold">Copyright &copy; {{ date('Y') }} RSUD dr. H. Moh. Anwar Sumenep</small>
                    <br>
                    <small class="text-muted" style="font-size: 0.75rem;">Developed by Vinda Sartika Basri</small>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- SCRIPT TOGGLE (Diperbaiki) --}}
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