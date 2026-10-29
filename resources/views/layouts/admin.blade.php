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
            --bg-main: #0B0914;
            --bg-surface: #151221;
            --bg-surface-hover: #1D192B;
            --bg-inner: #211D32;
            --text-primary: #FFFFFF;
            --text-secondary: #8A869D;
            --accent-primary: #7C3AED;
            --accent-gradient: linear-gradient(90deg, #6C38FF, #9B51E0);
            --border-color: rgba(255, 255, 255, 0.05);
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-main); 
            color: var(--text-primary);
            overflow-x: hidden; 
        }
        
        /* === SIDEBAR MODERN === */
        #sidebar-wrapper {
            width: 270px;
            height: 100vh;
            background: var(--bg-main);
            color: var(--text-primary);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
        }

        #sidebar-wrapper::-webkit-scrollbar { width: 4px; }
        #sidebar-wrapper::-webkit-scrollbar-track { background: transparent; }
        #sidebar-wrapper::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }

        /* === KONTEN UTAMA === */
        #page-content-wrapper {
            width: calc(100% - 270px);
            margin-left: 270px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--bg-main);
        }

        /* === HEADER / NAVBAR === */
        .main-header {
            background: var(--bg-main);
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* === LOGIKA TOGGLE === */
        body.toggled #sidebar-wrapper { margin-left: -270px; }
        body.toggled #page-content-wrapper { width: 100%; margin-left: 0; }

        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -270px; }
            #page-content-wrapper { width: 100%; margin-left: 0; }
            body.toggled #sidebar-wrapper { margin-left: 0; }
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(11, 9, 20, 0.8); backdrop-filter: blur(5px); z-index: 998; top:0; left:0; transition: 0.3s; }
            body.toggled .overlay { display: block; }
        }

        /* === STYLING MENU & SUBMENU === */
        .sidebar-header { padding: 25px 25px 20px; }
        .sidebar-label { padding: 15px 25px 5px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); font-weight: 600; }
        
        .list-group-item { 
            background: transparent; 
            color: var(--text-secondary); 
            border: none; 
            padding: 12px 20px; 
            margin: 4px 15px; 
            border-radius: 12px; 
            transition: all 0.25s ease; 
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
        }
        
        .list-group-item i { font-size: 1.1rem; transition: 0.3s; }

        .list-group-item:hover { 
            background: var(--bg-surface); 
            color: var(--text-primary); 
        }
        
        .list-group-item.active { 
            background: var(--accent-gradient) !important; 
            color: #ffffff !important; 
            font-weight: 500;
        }
        .list-group-item.active i { color: #ffffff !important; }

        /* Sub-menu */
        .sub-menu-wrapper { margin: 2px 12px; padding: 4px 0; }
        .sub-menu-item { padding: 8px 18px 8px 42px !important; font-size: 0.8rem !important; margin: 2px 6px !important; }

        /* Rotate Arrow */
        .menu-arrow { transition: transform 0.3s ease; font-size: 0.75rem !important; }
        .collapsed .menu-arrow { transform: rotate(-90deg); }

        /* Input Search Box */
        .sidebar-search { padding: 10px 20px 20px; }
        .sidebar-search .input-group-text, .sidebar-search .form-control {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        .sidebar-search .input-group-text { border-right: none; border-radius: 12px 0 0 12px; }
        .sidebar-search .form-control { border-left: none; border-radius: 0 12px 12px 0; font-size: 0.8rem; }
        .sidebar-search .form-control::placeholder { color: var(--text-secondary); }
        .sidebar-search .form-control:focus {
            box-shadow: none;
            background: var(--bg-surface-hover);
            border-color: var(--accent-primary);
            color: var(--text-primary);
        }

        /* === ANIMASI LONCENG & DROPDOWN === */
        @keyframes ring {
            0%, 50%, 100% { transform: rotate(0); }
            10%, 30% { transform: rotate(15deg); }
            20%, 40% { transform: rotate(-10deg); }
        }
        .bell-ring { animation: ring 2s ease infinite; display: inline-block; transform-origin: top center; color: #EF4444; }
        
        .dropdown-menu {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .dropdown-menu .dropdown-item { color: var(--text-secondary); }
        .dropdown-menu .dropdown-item:hover { background-color: var(--bg-inner); color: var(--text-primary); }
        
        /* Utility overrides for dark mode */
        .text-dark { color: var(--text-primary) !important; }
        .bg-white { background-color: var(--bg-surface) !important; }
        .border-top { border-top: 1px solid var(--border-color) !important; }
        .border-bottom { border-bottom: 1px solid var(--border-color) !important; }
        .bg-light { background-color: var(--bg-inner) !important; }
        
        .btn-white { background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--text-primary); }
        .btn-white:hover { background: var(--bg-surface-hover); color: var(--text-primary); }
    </style>
</head>
<body>

    @php
        $besok = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        $logbookNotifs = \App\Models\Logbook::where('tanggal_acara', $besok)->get();
        $notifCount = $logbookNotifs->count();
    @endphp
    
    <div class="overlay" id="overlayClick"></div>

    <div id="wrapper">
        
        {{-- SIDEBAR --}}
        <nav id="sidebar-wrapper">
            <div class="sidebar-header d-flex align-items-center mb-2">
                <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px; background: var(--accent-gradient);">
                    <i class="bi bi-hospital text-white fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-6 text-white tracking-wide">IPP-RSUD</div>
                    <small class="text-secondary" style="font-size: 0.65rem;">dr. H. Moh. Anwar</small>
                </div>
            </div>

            {{-- FORM PENCARIAN MENU --}}
            <div class="sidebar-search">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="menuSearchInput" class="form-control" placeholder="Search menu..." autocomplete="off">
                </div>
            </div>
            
            <div class="list-group list-group-flush pb-4" id="sidebarMenuList">
                <div class="sidebar-label menu-group-label">Main</div>

                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('utama.dashboard') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('utama.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door me-3"></i> <span>Dashboard</span>
                    </a>
                @endif
                
                <div class="sidebar-label menu-group-label mt-3">Layanan Publik</div>

                <a href="#submenuPengaduan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard', 'complaints.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-chat-left-text me-3"></i> <span>Pengaduan</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard', 'complaints.*') ? 'show' : '' }}" id="submenuPengaduan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <span>Dashboard Pengaduan</span>
                            </a>
                        @endif
                        <a href="{{ route('complaints.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                            <span>Data Keluhan</span>
                        </a>
                    </div>
                </div>

                <a href="#submenuPermintaan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard.permintaan', 'permintaan.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-file-earmark-medical me-3"></i> <span>Layanan Informasi</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.permintaan', 'permintaan.*') ? 'show' : '' }}" id="submenuPermintaan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard.permintaan') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard.permintaan') ? 'active' : '' }}">
                                <span>Dashboard Informasi</span>
                            </a>
                        @endif
                        <a href="{{ route('permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permintaan.index') ? 'active' : '' }}">
                            <span>Data Permintaan</span>
                        </a>
                        <a href="{{ route('permohonan-informasi.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-informasi.*') ? 'active' : '' }}">
                            <span>Permohonan Informasi</span>
                        </a>
                        <a href="{{ route('permohonan-uhc.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-uhc.*') ? 'active' : '' }}">
                            <span>Peralihan UHC</span>
                        </a>
                    </div>
                </div>

                <a href="#submenuLasehat" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('lasehat.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-truck me-3"></i> <span>Mobil LaSehat</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lasehat.*') ? 'show' : '' }}" id="submenuLasehat">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('lasehat.dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.dashboard') ? 'active' : '' }}">
                                <span>Dashboard LaSehat</span>
                            </a>
                        @endif
                        <a href="{{ route('lasehat.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.index') ? 'active' : '' }}">
                            <span>Data Mobil LaSehat</span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-label menu-group-label mt-3">Other</div>

                <a href="#submenuLogbook" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('logbook.*', 'whatsapp.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-journal-text me-3"></i> <span>Logbook & WA</span></div>
                    <div class="d-flex align-items-center">
                        @if($notifCount > 0) <span class="badge bg-danger rounded-pill me-2">{{ $notifCount }}</span> @endif
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </div>
                </a>
                <div class="collapse {{ request()->routeIs('logbook.*', 'whatsapp.*') ? 'show' : '' }}" id="submenuLogbook">
                    <div class="sub-menu-wrapper">
                        <a href="{{ route('logbook.index') }}" class="list-group-item sub-menu-item sidebar-nav-item d-flex justify-content-between {{ request()->routeIs('logbook.*') ? 'active' : '' }}">
                            <span>Kalender Logbook</span>
                            @if($notifCount > 0) <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span> @endif
                        </a>
                        <a href="{{ route('whatsapp.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
                            <span>Koneksi WhatsApp</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('bpjs.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('bpjs.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card me-3"></i> <span>Integrasi BPJS</span>
                </a>

                @if(auth()->user()->role == 'admin')
                    <a href="#submenuMaster" data-bs-toggle="collapse" 
                        class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*') ? '' : 'collapsed' }}">
                        <div><i class="bi bi-shield-lock me-3"></i> <span>Data Master</span></div>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*') ? 'show' : '' }}" id="submenuMaster">
                        <div class="sub-menu-wrapper">
                            <a href="{{ route('master.units.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.units.*') ? 'active' : '' }}"><span>Unit Tujuan</span></a>
                            <a href="{{ route('sources.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('sources.*') ? 'active' : '' }}"><span>Media Pengaduan</span></a>
                            <a href="{{ route('master.reporters.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.reporters.*') ? 'active' : '' }}"><span>Unit Pelapor</span></a>
                            <a href="{{ route('kategori-keluhan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('kategori-keluhan.*') ? 'active' : '' }}"><span>Kategori Keluhan</span></a>
                            <a href="{{ route('kategori-permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('kategori-permintaan.*') ? 'active' : '' }}"><span>Kategori Permintaan</span></a>
                            <a href="{{ route('master.supir.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.supir.*') ? 'active' : '' }}"><span>Master Supir</span></a>
                            <a href="{{ route('master.grades.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.grades.*') ? 'active' : '' }}"><span>Kegawatan</span></a>
                        </div>
                    </div>

                    <a href="{{ route('users.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-3"></i> <span>Kelola User</span>
                    </a>
                @endif
                
                {{-- Logout Mobile --}}
                <div class="mt-4 px-3 d-md-none">
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-pill">
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
                <div class="d-flex align-items-center">
                    <button class="btn btn-white border-0 me-3 rounded-2 d-flex align-items-center justify-content-center" id="menu-toggle" style="width: 35px; height: 35px;">
                        <i class="bi bi-list fs-5 text-white"></i>
                    </button>
                    <div>
                        <div class="text-secondary small fw-medium" style="font-size:0.75rem;">Main / Application</div>
                        <h5 class="fw-bold m-0 text-white" style="font-size:1.1rem;">
                            @yield('title', 'Dashboard Panel')
                        </h5>
                    </div>
                </div>
                
                {{-- KANAN: Lonceng Notif & Profil --}}
                <div class="d-flex align-items-center">
                    
                    {{-- DROPDOWN NOTIFIKASI --}}
                    <div class="dropdown me-3">
                        <a href="#" class="text-white position-relative d-flex align-items-center justify-content-center rounded-circle" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; background: var(--bg-surface); text-decoration: none;">
                            <i class="bi bi-bell fs-6 text-secondary {{ $notifCount > 0 ? 'bell-ring' : '' }}"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem; border: 2px solid var(--bg-main);">
                                    {{ $notifCount }}
                                </span>
                            @endif
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-3 notif-dropdown pb-0" aria-labelledby="notifDropdown" style="width: 320px; border-radius: 12px; border-color: var(--border-color)!important;">
                            <li class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-surface); border-radius: 12px 12px 0 0;">
                                <span class="fw-bold text-white mb-0">Pengingat Logbook</span>
                                <span class="badge bg-danger rounded-pill">{{ $notifCount }} H-1</span>
                            </li>
                            <div style="max-height: 280px; overflow-y: auto; background: var(--bg-main);" class="py-2">
                                @forelse($logbookNotifs as $notif)
                                <li>
                                    <a class="dropdown-item notif-item d-flex align-items-start py-2 px-3" href="{{ route('logbook.index') }}">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3 mt-1" style="width: 35px; height: 35px; flex-shrink: 0;">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div style="overflow: hidden;">
                                            <div class="fw-semibold text-truncate text-white" style="font-size: 0.85rem;">{{ $notif->judul_acara }}</div>
                                            <div class="text-secondary text-truncate" style="font-size: 0.75rem;">{{ $notif->deskripsi ?? 'Tidak ada deskripsi' }}</div>
                                            <small class="text-danger fw-bold" style="font-size: 0.7rem;">Besok - {{ \Carbon\Carbon::parse($notif->tanggal_acara)->format('d M Y') }}</small>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="p-4 text-center">
                                    <i class="bi bi-check2-circle text-secondary fs-1 mb-2 d-block"></i>
                                    <small class="text-secondary fw-medium">Tidak ada agenda H-1 untuk besok.</small>
                                </li>
                                @endforelse
                            </div>
                            <li class="border-top" style="background: var(--bg-surface); border-radius: 0 0 12px 12px;">
                                <a href="{{ route('logbook.index') }}" class="dropdown-item text-center text-primary fw-bold py-2" style="font-size: 0.85rem; color: #9B51E0 !important;">
                                    Buka Kalender Logbook
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- DROPDOWN PROFIL --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-white px-3 py-1 rounded-pill" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6C38FF&color=fff&bold=true" class="rounded-circle me-2" width="30" height="30">
                            <div class="d-none d-md-block text-start me-1" style="line-height: 1.2;">
                                <div class="small fw-semibold" style="font-size: 0.8rem;">{{ Auth::user()->name }}</div>
                                <div class="text-secondary text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ Auth::user()->role }}</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-3" style="border-radius: 12px; border-color: var(--border-color)!important;" aria-labelledby="dropdownUser1">
                            <li><h6 class="dropdown-header text-white fw-bold">Halo, {{ Auth::user()->name }}!</h6></li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear me-2"></i> Profil & Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary opacity-25"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
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
            <footer class="text-center py-3 mt-auto border-top border-secondary border-opacity-10" style="background: transparent;">
                <div class="container">
                    <small class="text-secondary fw-medium">Copyright &copy; {{ date('Y') }} RSUD dr. H. Moh. Anwar Sumenep</small>
                    <br>
                    <small class="text-secondary" style="font-size: 0.7rem;">Developed by <span class="fw-bold" style="color: #9B51E0;">Vinda Sartika Basri</span></small>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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

            const searchInput = document.getElementById("menuSearchInput");
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    const filter = this.value.toLowerCase().trim();
                    const menuItems = document.querySelectorAll(".sidebar-nav-item");
                    const menuToggles = document.querySelectorAll(".sidebar-nav-toggle");
                    const groupLabels = document.querySelectorAll(".menu-group-label");

                    if (filter === "") {
                        menuItems.forEach(el => el.style.display = "");
                        menuToggles.forEach(el => el.style.display = "");
                        groupLabels.forEach(el => el.style.display = "");
                        return;
                    }

                    groupLabels.forEach(el => el.style.display = "none");

                    menuItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            item.style.display = "";
                            const parentCollapse = item.closest(".collapse");
                            if (parentCollapse) {
                                parentCollapse.classList.add("show");
                                const parentToggle = document.querySelector(`[href="#${parentCollapse.id}"]`);
                                if (parentToggle) {
                                    parentToggle.classList.remove("collapsed");
                                    parentToggle.style.display = "";
                                }
                            }
                        } else {
                            item.style.display = "none";
                        }
                    });

                    menuToggles.forEach(toggle => {
                        const targetId = toggle.getAttribute("href");
                        if (targetId && targetId.startsWith("#")) {
                            const collapseEl = document.querySelector(targetId);
                            if (collapseEl) {
                                const visibleChildren = collapseEl.querySelectorAll(".sidebar-nav-item:not([style*='display: none'])");
                                toggle.style.display = visibleChildren.length === 0 ? "none" : "";
                            }
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>