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
        :root {
            --primary-color: #f25c3a;
            --primary-hover: #e04b2a;
            --bg-body: #f4f6f8;
            --surface: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-body); 
            overflow-x: hidden; 
            color: var(--text-main);
        }
        
        /* === SIDEBAR MODERN (LIGHT THEME) === */
        #sidebar-wrapper {
            width: 260px;
            height: 100vh;
            background: var(--surface);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
        }

        #sidebar-wrapper::-webkit-scrollbar { width: 4px; }
        #sidebar-wrapper::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        /* === KONTEN UTAMA === */
        #page-content-wrapper {
            width: calc(100% - 260px);
            margin-left: 260px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* === HEADER / NAVBAR === */
        .main-header {
            background: var(--bg-body);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* === LOGIKA TOGGLE === */
        body.toggled #sidebar-wrapper { margin-left: -260px; }
        body.toggled #page-content-wrapper { width: 100%; margin-left: 0; }

        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -260px; }
            #page-content-wrapper { width: 100%; margin-left: 0; }
            body.toggled #sidebar-wrapper { margin-left: 0; }
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.4); z-index: 998; top:0; left:0; }
            body.toggled .overlay { display: block; }
        }

        /* === STYLING MENU & SUBMENU === */
        .sidebar-header { 
            padding: 24px 20px; 
            margin-bottom: 10px;
        }
        
        .sidebar-label { 
            padding: 16px 24px 8px; 
            font-size: 0.75rem; 
            color: #9ca3af; 
            font-weight: 500; 
        }
        
        .list-group-item { 
            background: transparent; 
            color: var(--text-muted); 
            border: none; 
            padding: 10px 18px; 
            margin: 2px 16px; 
            border-radius: 8px; 
            transition: all 0.2s ease; 
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .list-group-item i { font-size: 1.1rem; transition: 0.3s; color: #9ca3af; }

        .list-group-item:hover { 
            background: #f9fafb; 
            color: var(--text-main); 
        }
        
        .list-group-item.active { 
            background: var(--primary-color) !important; 
            color: #ffffff !important; 
            box-shadow: 0 4px 12px rgba(242, 92, 58, 0.2);
        }
        .list-group-item.active i { color: #ffffff !important; }

        /* Sub-menu Indentation */
        .sub-menu-wrapper { margin: 2px 12px; padding: 4px 0; }
        .sub-menu-item { padding: 8px 18px 8px 42px !important; font-size: 0.85rem !important; margin: 2px 6px !important; }

        .menu-arrow { transition: transform 0.3s ease; font-size: 0.75rem !important; }
        .collapsed .menu-arrow { transform: rotate(-90deg); }

        /* Search Nav */
        .nav-search { background: var(--surface); border-radius: 20px; padding: 5px 15px; border: 1px solid var(--border-color); display: flex; align-items: center; min-width: 300px; }
        .nav-search input { border: none; background: transparent; outline: none; width: 100%; margin-left: 10px; font-size: 0.85rem; }
        
        .header-icon {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border-color); border-radius: 50%;
            color: var(--text-muted); text-decoration: none; transition: 0.2s;
            background: var(--surface);
        }
        .header-icon:hover { background: #f9fafb; }

    </style>
</head>
<body>

    @php
        $besok = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        $logbookNotifs = \App\Models\Logbook::where('tanggal_acara', $besok)->get() ?? collect();
        $notifCount = $logbookNotifs->count();
    @endphp
    
    <div class="overlay" id="overlayClick"></div>

    <div id="wrapper">
        
        {{-- SIDEBAR --}}
        <nav id="sidebar-wrapper">
            <div class="sidebar-header d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: var(--primary-color);">
                    <i class="bi bi-hospital-fill text-white fs-5"></i>
                </div>
                <div class="fw-bold fs-5 text-dark">Finexy<span style="font-size:10px; color:#888;">RSUD</span></div>
            </div>
            
            <div class="list-group list-group-flush pb-4 mt-1" id="sidebarMenuList">
                <div class="sidebar-label menu-group-label">Menu</div>

                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('utama.dashboard') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('utama.dashboard') ? 'active' : '' }} mb-2">
                        <i class="bi bi-grid-fill me-3"></i> <span>Dashboard</span>
                    </a>
                @endif
                
                {{-- KELOMPOK MENU: LAYANAN PUBLIK --}}
                <div class="sidebar-label menu-group-label">Layanan Publik</div>

                {{-- Submenu 1: Pengaduan --}}
                <a href="#submenuPengaduan" data-bs-toggle="collapse" class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard', 'complaints.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-chat-square-text me-3"></i> <span>Pengaduan</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard', 'complaints.*') ? 'show' : '' }}" id="submenuPengaduan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard Pengaduan</a>
                        @endif
                        <a href="{{ route('complaints.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">Data Keluhan</a>
                    </div>
                </div>

                {{-- Submenu 2: Layanan Informasi --}}
                <a href="#submenuPermintaan" data-bs-toggle="collapse" class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-file-earmark-text me-3"></i> <span>Informasi</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-*') ? 'show' : '' }}" id="submenuPermintaan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard.permintaan') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard.permintaan') ? 'active' : '' }}">Dashboard Info</a>
                        @endif
                        <a href="{{ route('permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permintaan.index') ? 'active' : '' }}">Data Permintaan</a>
                        <a href="{{ route('permohonan-informasi.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-informasi.*') ? 'active' : '' }}">Permohonan Info</a>
                        <a href="{{ route('permohonan-uhc.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-uhc.*') ? 'active' : '' }}">Peralihan UHC</a>
                    </div>
                </div>

                {{-- Submenu 3: Mobil LaSehat --}}
                <a href="#submenuLasehat" data-bs-toggle="collapse" class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('lasehat.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-car-front me-3"></i> <span>LaSehat</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lasehat.*') ? 'show' : '' }}" id="submenuLasehat">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('lasehat.dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.dashboard') ? 'active' : '' }}">Dashboard LaSehat</a>
                        @endif
                        <a href="{{ route('lasehat.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.index') ? 'active' : '' }}">Data Mobil</a>
                    </div>
                </div>

                <div class="sidebar-label menu-group-label mt-2">Integrasi & Agenda</div>
                
                <a href="#submenuLogbook" data-bs-toggle="collapse" class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('logbook.*', 'whatsapp.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-calendar-event me-3"></i> <span>Agenda & WA</span></div>
                    <div class="d-flex align-items-center">
                        @if($notifCount > 0)
                            <span class="badge bg-danger rounded-pill me-2" style="font-size: 0.65rem;">{{ $notifCount }}</span>
                        @endif
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </div>
                </a>
                <div class="collapse {{ request()->routeIs('logbook.*', 'whatsapp.*') ? 'show' : '' }}" id="submenuLogbook">
                    <div class="sub-menu-wrapper">
                        <a href="{{ route('logbook.index') }}" class="list-group-item sub-menu-item sidebar-nav-item d-flex justify-content-between align-items-center {{ request()->routeIs('logbook.*') ? 'active' : '' }}">
                            <span>Kalender Logbook</span>
                            @if($notifCount > 0) <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span> @endif
                        </a>
                        <a href="{{ route('whatsapp.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">Koneksi WA</a>
                    </div>
                </div>

                <a href="{{ route('bpjs.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('bpjs.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card me-3"></i> <span>BPJS Kepesertaan</span>
                </a>

                @if(auth()->user()->role == 'admin')
                    <div class="sidebar-label menu-group-label mt-2">Pengaturan</div>
                    
                    <a href="#submenuMaster" data-bs-toggle="collapse" class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('master.*', 'sources.*', 'kategori-*') ? '' : 'collapsed' }}">
                        <div><i class="bi bi-gear me-3"></i> <span>Data Master</span></div>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('master.*', 'sources.*', 'kategori-*') ? 'show' : '' }}" id="submenuMaster">
                        <div class="sub-menu-wrapper">
                            <a href="{{ route('master.units.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Unit Tujuan</a>
                            <a href="{{ route('sources.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Media Pengaduan</a>
                            <a href="{{ route('master.reporters.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Unit Pelapor</a>
                            <a href="{{ route('kategori-keluhan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Kategori Keluhan</a>
                            <a href="{{ route('kategori-permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Kategori Permintaan</a>
                            <a href="{{ route('master.supir.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Master Supir</a>
                            <a href="{{ route('master.grades.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Kegawatan</a>
                        </div>
                    </div>

                    <a href="{{ route('users.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-3"></i> <span>Kelola User</span>
                    </a>
                @endif
                
                {{-- Logout Button in Sidebar --}}
                <div class="mt-4 px-3">
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn w-100 rounded-3 shadow-sm d-flex align-items-center justify-content-center fw-medium" style="background: rgba(242, 92, 58, 0.1); color: var(--primary-color);">
                            <i class="bi bi-box-arrow-right me-2"></i> Log out
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- KONTEN HALAMAN --}}
        <div id="page-content-wrapper">
            
            {{-- HEADER --}}
            <nav class="main-header">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light border-0 shadow-sm me-3 d-md-none rounded-circle d-flex align-items-center justify-content-center" id="menu-toggle" style="width: 40px; height: 40px;">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    
                    {{-- Search moved to Nav --}}
                    <div class="nav-search d-none d-md-flex">
                        <i class="bi bi-search text-muted"></i>
                        <input type="text" placeholder="Search menu...">
                        <span class="text-muted ms-2" style="font-size:0.7rem; background:#f3f4f6; padding:2px 6px; border-radius:4px;">K ⌘</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    
                    <a href="#" class="header-icon position-relative">
                        <i class="bi bi-bell"></i>
                        @if($notifCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        @endif
                    </a>

                    <a href="#" class="header-icon d-none d-md-flex">
                        <i class="bi bi-question-circle"></i>
                    </a>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown">
                            <div class="d-none d-md-block text-end me-2" style="line-height: 1.2;">
                                <div class="fw-bold" style="font-size: 0.85rem;">{{ Auth::user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ Auth::user()->role }}</div>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=f25c3a&color=fff" class="rounded-circle" width="38" height="38">
                            <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 0.75rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-3">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i> Log out</button>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toggleBtn = document.getElementById("menu-toggle");
            var overlay = document.getElementById("overlayClick");
            function toggleMenu(e) {
                e.preventDefault(); document.body.classList.toggle("toggled");
            }
            if(toggleBtn) toggleBtn.addEventListener("click", toggleMenu);
            if(overlay) overlay.addEventListener("click", toggleMenu);
        });
    </script>
</body>
</html>