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
            --primary-purple: #5542F6;
            --bg-body: #F4F7FE;
            --text-dark: #2B3674;
            --text-gray: #A3AED0;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-body); 
            overflow-x: hidden; 
            color: var(--text-dark);
        }
        
        /* === SIDEBAR MODERN (Sesuai Gambar) === */
        #sidebar-wrapper {
            width: 280px;
            height: 100vh;
            background-color: var(--primary-purple);
            /* Ornamen Lingkaran Abstrak */
            background-image: 
                radial-gradient(circle at 10% 80%, rgba(255,255,255,0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 10%, rgba(255,255,255,0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255,255,255,0.05) 0%, transparent 60%);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            border-top-right-radius: 30px;
            border-bottom-right-radius: 30px;
        }

        #sidebar-wrapper::-webkit-scrollbar { width: 0px; } /* Hide scrollbar for clean look */

        /* Profil di dalam Sidebar */
        .sidebar-profile {
            padding: 30px 20px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .sidebar-profile img {
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.2);
        }
        .sidebar-profile-info h6 {
            margin: 0;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
        }
        .sidebar-profile-info span {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.7);
        }

        /* === KONTEN UTAMA === */
        #page-content-wrapper {
            width: calc(100% - 280px);
            margin-left: 280px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* === HEADER / NAVBAR MINIMALIS === */
        .main-header {
            background: transparent;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
        }

        /* === LOGIKA TOGGLE === */
        body.toggled #sidebar-wrapper { margin-left: -280px; }
        body.toggled #page-content-wrapper { width: 100%; margin-left: 0; }

        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -280px; border-radius: 0; }
            #page-content-wrapper { width: 100%; margin-left: 0; }
            body.toggled #sidebar-wrapper { margin-left: 0; }
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(3px); z-index: 998; top:0; left:0; transition: 0.3s; }
            body.toggled .overlay { display: block; }
        }

        /* === STYLING MENU & SUBMENU === */
        .sidebar-label { padding: 15px 30px 5px; font-size: 0.7rem; color: rgba(255,255,255,0.6); font-weight: 500; }
        
        .list-group-item { 
            background: transparent; 
            color: rgba(255,255,255,0.7); 
            border: none; 
            padding: 12px 20px 12px 30px; 
            margin: 4px 0 4px 20px; /* Margin kiri untuk memberi ruang lengkungan */
            border-radius: 30px 0 0 30px; /* Lengkung kiri, rata kanan */
            transition: all 0.3s ease; 
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
            position: relative;
        }
        
        .list-group-item i { font-size: 1.2rem; transition: 0.3s; width: 30px; }

        .list-group-item:hover:not(.active) { 
            color: #ffffff; 
            background: rgba(255,255,255,0.1);
        }
        
        /* EFEK CUTOUT (LUBANG) UNTUK MENU AKTIF */
        .list-group-item.active { 
            background: var(--bg-body) !important; 
            color: var(--primary-purple) !important; 
            font-weight: 600;
        }
        .list-group-item.active i { color: var(--primary-purple) !important; }

        /* Pseudo-elemen untuk membuat lengkungan luar (cutout effect) */
        .list-group-item.active::before,
        .list-group-item.active::after {
            content: '';
            position: absolute;
            right: 0;
            width: 30px;
            height: 30px;
            background-color: transparent;
        }
        .list-group-item.active::before {
            top: -30px;
            border-bottom-right-radius: 30px;
            box-shadow: 15px 15px 0 var(--bg-body);
        }
        .list-group-item.active::after {
            bottom: -30px;
            border-top-right-radius: 30px;
            box-shadow: 15px -15px 0 var(--bg-body);
        }

        /* Sub-menu styling */
        .sub-menu-wrapper { margin: 0 0 0 20px; }
        .sub-menu-item {
            padding: 10px 15px 10px 45px !important;
            font-size: 0.85rem !important;
            margin: 2px 0 2px 10px !important;
        }

        .menu-arrow { transition: transform 0.3s ease; font-size: 0.8rem !important; }
        .collapsed .menu-arrow { transform: rotate(-90deg); }

        /* Input Search Box */
        .sidebar-search { padding: 0 20px 15px 20px; }
        .sidebar-search .form-control, .sidebar-search .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
        }
        .sidebar-search .input-group-text { border-radius: 20px 0 0 20px; padding-left: 15px; }
        .sidebar-search .form-control { border-radius: 0 20px 20px 0; font-size: 0.85rem; }
        .sidebar-search .form-control::placeholder { color: rgba(255,255,255,0.6); }
        .sidebar-search .form-control:focus { box-shadow: none; background: rgba(255, 255, 255, 0.2); }

        /* === ANIMASI LONCENG === */
        @keyframes ring {
            0%, 100% { transform: rotate(0); }
            10%, 30% { transform: rotate(15deg); }
            20%, 40% { transform: rotate(-10deg); }
            50% { transform: rotate(0); }
        }
        .bell-ring { animation: ring 2s ease infinite; display: inline-block; transform-origin: top center; color: #ef4444; }
        
        /* Global Card Style */
        .card { border-radius: 24px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #fff; }
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
            {{-- User Profile dipindah ke dalam Sidebar --}}
            <div class="sidebar-profile">
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=ffffff&color=5542F6&bold=true" width="50" height="50">
                <div class="sidebar-profile-info">
                    <h6>{{ Auth::user()->name }}</h6>
                    <span>{{ Auth::user()->role }}</span>
                </div>
            </div>

            {{-- FORM PENCARIAN MENU --}}
            <div class="sidebar-search mt-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search text-white-50"></i></span>
                    <input type="text" id="menuSearchInput" class="form-control text-white" placeholder="Cari menu..." autocomplete="off">
                </div>
            </div>
            
            <div class="list-group list-group-flush pb-4 mt-2" id="sidebarMenuList">
                {{-- MENU DASHBOARD UTAMA --}}
                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('utama.dashboard') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('utama.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i> <span>Home</span>
                    </a>
                @endif
                
                {{-- KELOMPOK MENU: LAYANAN PUBLIK --}}
                <div class="sidebar-label menu-group-label mt-2">Layanan Publik</div>

                <a href="#submenuPengaduan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard', 'complaints.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-activity"></i> <span>Pengaduan</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard', 'complaints.*') ? 'show' : '' }}" id="submenuPengaduan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard Utama</span>
                            </a>
                        @endif
                        <a href="{{ route('complaints.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                            <i class="bi bi-table"></i> <span>Data Keluhan</span>
                        </a>
                    </div>
                </div>

                <a href="#submenuPermintaan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-informasi.*', 'permohonan-uhc.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-clipboard2-data"></i> <span>Layanan Informasi</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-informasi.*', 'permohonan-uhc.*') ? 'show' : '' }}" id="submenuPermintaan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard.permintaan') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard.permintaan') ? 'active' : '' }}">
                                <i class="bi bi-pie-chart-fill"></i> <span>Dashboard Info</span>
                            </a>
                        @endif
                        <a href="{{ route('permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permintaan.index') ? 'active' : '' }}">
                            <i class="bi bi-journal-text"></i> <span>Data Permintaan</span>
                        </a>
                        <a href="{{ route('permohonan-informasi.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-informasi.*') ? 'active' : '' }}">
                            <i class="bi bi-envelope-paper"></i> <span>Permohonan Info</span>
                        </a>
                        <a href="{{ route('permohonan-uhc.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-uhc.*') ? 'active' : '' }}">
                            <i class="bi bi-hospital"></i> <span>Peralihan UHC</span>
                        </a>
                    </div>
                </div>

                <a href="#submenuLasehat" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('lasehat.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-car-front-fill"></i> <span>Mobil LaSehat</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lasehat.*') ? 'show' : '' }}" id="submenuLasehat">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('lasehat.dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                            </a>
                        @endif
                        <a href="{{ route('lasehat.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.index') ? 'active' : '' }}">
                            <i class="bi bi-table"></i> <span>Data LaSehat</span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-label menu-group-label mt-2">Lainnya</div>

                <a href="#submenuLogbook" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('logbook.*', 'whatsapp.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-clock-history"></i> <span>History / WA</span></div>
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
                            <div><i class="bi bi-calendar-check"></i> <span>Logbook</span></div>
                            @if($notifCount > 0)
                                <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('whatsapp.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
                            <i class="bi bi-whatsapp"></i> <span>Koneksi WA</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('bpjs.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('bpjs.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front-fill"></i> <span>Integrasi BPJS</span>
                </a>

                @if(auth()->user()->role == 'admin')
                    <a href="#submenuMaster" data-bs-toggle="collapse" 
                        class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*', 'users.*') ? '' : 'collapsed' }}">
                        <div><i class="bi bi-gear-fill"></i> <span>Settings</span></div>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*', 'users.*') ? 'show' : '' }}" id="submenuMaster">
                        <div class="sub-menu-wrapper">
                            <a href="{{ route('master.units.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.units.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Unit Tujuan</span></a>
                            <a href="{{ route('sources.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('sources.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Media Pengaduan</span></a>
                            <a href="{{ route('master.reporters.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.reporters.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Unit Pelapor</span></a>
                            <a href="{{ route('kategori-keluhan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('kategori-keluhan.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Kategori Keluhan</span></a>
                            <a href="{{ route('kategori-permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('kategori-permintaan.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Kat. Permintaan</span></a>
                            <a href="{{ route('master.supir.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.supir.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Master Supir</span></a>
                            <a href="{{ route('master.grades.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.grades.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Kegawatan</span></a>
                            <a href="{{ route('users.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-circle"></i> <span>Kelola User</span></a>
                        </div>
                    </div>
                @endif
                
                {{-- Logout Button --}}
                <div class="mt-4 px-4">
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-light w-100 btn-sm rounded-pill shadow-sm text-danger fw-bold">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- KONTEN HALAMAN --}}
        <div id="page-content-wrapper">
            
            {{-- HEADER MINIMALIS --}}
            <nav class="main-header">
                <div class="d-flex align-items-center">
                    <button class="btn border-0 me-3 rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" id="menu-toggle" style="width: 45px; height: 45px; color: var(--text-dark);">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h4 class="fw-bold m-0" style="color: var(--text-dark);">
                        @yield('title', 'Dashboard')
                    </h4>
                </div>
                
                <div class="d-flex align-items-center">
                    {{-- DROPDOWN NOTIFIKASI --}}
                    <div class="dropdown me-2">
                        <a href="#" class="position-relative d-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 45px; height: 45px; text-decoration: none;">
                            <i class="bi bi-bell-fill fs-5 text-muted {{ $notifCount > 0 ? 'bell-ring' : '' }}"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.65rem;">
                                    {{ $notifCount }}
                                </span>
                            @endif
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 pb-0" aria-labelledby="notifDropdown" style="width: 320px; border-radius: 16px;">
                            <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light" style="border-radius: 16px 16px 0 0;">
                                <span class="fw-bold mb-0 text-dark">Notifikasi Logbook</span>
                            </li>
                            <div style="max-height: 280px; overflow-y: auto;" class="py-2">
                                @forelse($logbookNotifs as $notif)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start py-2" href="{{ route('logbook.index') }}">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 mt-1" style="width: 35px; height: 35px; flex-shrink: 0;">
                                            <i class="bi bi-calendar-check-fill"></i>
                                        </div>
                                        <div style="overflow: hidden;">
                                            <div class="fw-semibold text-truncate text-dark" style="font-size: 0.85rem;">{{ $notif->judul_acara }}</div>
                                            <small class="text-danger fw-bold" style="font-size: 0.7rem;">Besok - {{ \Carbon\Carbon::parse($notif->tanggal_acara)->format('d M Y') }}</small>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="p-4 text-center">
                                    <small class="text-muted fw-medium">Tidak ada notifikasi baru.</small>
                                </li>
                                @endforelse
                            </div>
                        </ul>
                    </div>
                </div>
            </nav>

            {{-- ISI KONTEN --}}
            <div class="container-fluid p-4 pt-2">
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
                });
            }
        });
    </script>
</body>
</html>