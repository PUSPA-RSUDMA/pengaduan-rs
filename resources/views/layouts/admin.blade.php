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
            --primary-green: #164e32; /* Dark green from design */
            --primary-green-light: #226f48;
            --bg-color: #f7f9fa;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border-color: #f3f4f6;
            --radius-lg: 16px;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-color); 
            overflow-x: hidden; 
            color: var(--text-dark);
        }
        
        /* === SIDEBAR MODERN === */
        #sidebar-wrapper {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            color: var(--text-dark);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
        }

        #sidebar-wrapper::-webkit-scrollbar { width: 4px; }
        #sidebar-wrapper::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

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
            background: var(--bg-color);
            padding: 20px 30px;
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
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0, 0.3); z-index: 998; top:0; left:0; transition: 0.3s; }
            body.toggled .overlay { display: block; }
        }

        /* === STYLING MENU & SUBMENU === */
        .sidebar-header { 
            padding: 30px 24px 20px; 
            display: flex; 
            align-items: center;
        }
        
        .brand-icon {
            color: var(--primary-green);
            font-size: 1.8rem;
            margin-right: 10px;
            line-height: 1;
        }

        .sidebar-label { 
            padding: 15px 24px 8px; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: #9ca3af; 
            font-weight: 600; 
        }
        
        .list-group-item { 
            background: transparent; 
            color: var(--text-muted); 
            border: none; 
            padding: 10px 16px; 
            margin: 4px 16px; 
            border-radius: 12px; 
            transition: all 0.2s ease; 
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .list-group-item i { 
            font-size: 1.1rem; 
            transition: 0.3s; 
            width: 24px;
        }

        .list-group-item:hover { 
            background: #f3f4f6; 
            color: var(--text-dark); 
        }
        
        .list-group-item.active { 
            background: var(--primary-green) !important; 
            color: #ffffff !important; 
            box-shadow: 0 4px 12px rgba(22, 78, 50, 0.2);
        }

        .sub-menu-wrapper {
            margin: 2px 16px;
            padding: 4px 0;
            border-left: 2px solid #f3f4f6;
            margin-left: 32px;
        }

        .sub-menu-item {
            padding: 8px 16px !important;
            font-size: 0.85rem !important;
            margin: 2px 0 2px 12px !important;
        }

        .menu-arrow { transition: transform 0.3s ease; font-size: 0.75rem !important; }
        .collapsed .menu-arrow { transform: rotate(-90deg); }

        /* Input Search Box (Header style) */
        .header-search {
            background: #ffffff;
            border-radius: 20px;
            padding: 6px 16px;
            display: flex;
            align-items: center;
            width: 300px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border: 1px solid var(--border-color);
        }
        .header-search input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.85rem;
            width: 100%;
            margin-left: 10px;
            color: var(--text-dark);
        }
        .header-search input::placeholder { color: #9ca3af; }

        /* Top Icons */
        .top-icon-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-dark);
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            transition: 0.2s;
        }
        .top-icon-btn:hover { background: #f3f4f6; color: var(--text-dark); }

        /* Mobile App Promo Card (Mimicking "Donezo" layout) */
        .promo-card {
            background: var(--primary-green);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin: 20px 16px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .promo-card h6 { font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; }
        .promo-card p { font-size: 0.75rem; color: #d1d5db; margin-bottom: 15px; }
        .promo-btn {
            background: var(--primary-green-light);
            color: white;
            border: none;
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: 0.2s;
        }
        .promo-btn:hover { background: #2f8e5c; }
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
            <div class="sidebar-header">
                <i class="bi bi-bullseye brand-icon"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">IPP-RSUD</h5>
                </div>
            </div>

            <div class="list-group list-group-flush pb-4 mt-1" id="sidebarMenuList">
                <div class="sidebar-label menu-group-label">Menu</div>
                
                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('utama.dashboard') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('utama.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill me-2"></i> <span>Dashboard</span>
                    </a>
                @endif
                
                <div class="sidebar-label menu-group-label mt-2">Layanan Publik</div>

                {{-- Submenu 1: Pengaduan --}}
                <a href="#submenuPengaduan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard', 'complaints.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-chat-square-text me-2"></i> <span>Pengaduan</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard', 'complaints.*') ? 'show' : '' }}" id="submenuPengaduan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                Dashboard Utama
                            </a>
                        @endif
                        <a href="{{ route('complaints.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
                            Data Keluhan
                        </a>
                    </div>
                </div>

                {{-- Submenu 2: Layanan Informasi --}}
                <a href="#submenuPermintaan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-informasi.*', 'permohonan-uhc.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-journal-text me-2"></i> <span>Informasi</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-informasi.*', 'permohonan-uhc.*') ? 'show' : '' }}" id="submenuPermintaan">
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
                <a href="#submenuLasehat" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('lasehat.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-truck me-2"></i> <span>LaSehat</span></div>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lasehat.*') ? 'show' : '' }}" id="submenuLasehat">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('lasehat.dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.dashboard') ? 'active' : '' }}">Dashboard</a>
                        @endif
                        <a href="{{ route('lasehat.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.index') ? 'active' : '' }}">Data Mobil</a>
                    </div>
                </div>

                <div class="sidebar-label menu-group-label mt-2">General</div>

                <a href="#submenuLogbook" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('logbook.*', 'whatsapp.*') ? '' : 'collapsed' }}">
                    <div><i class="bi bi-calendar-event me-2"></i> <span>Agenda</span></div>
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
                            <span>Kalender</span>
                            @if($notifCount > 0) <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span> @endif
                        </a>
                        <a href="{{ route('whatsapp.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">Koneksi WA</a>
                    </div>
                </div>

                <a href="{{ route('bpjs.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('bpjs.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front me-2"></i> <span>Integrasi BPJS</span>
                </a>

                @if(auth()->user()->role == 'admin')
                    <a href="#submenuMaster" data-bs-toggle="collapse" 
                        class="list-group-item sidebar-nav-toggle d-flex justify-content-between align-items-center {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*') ? '' : 'collapsed' }}">
                        <div><i class="bi bi-gear me-2"></i> <span>Pengaturan</span></div>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*') ? 'show' : '' }}" id="submenuMaster">
                        <div class="sub-menu-wrapper">
                            <a href="{{ route('master.units.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Unit Tujuan</a>
                            <a href="{{ route('sources.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Media Pengaduan</a>
                            <a href="{{ route('master.reporters.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Unit Pelapor</a>
                            <a href="{{ route('kategori-keluhan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Kat. Keluhan</a>
                            <a href="{{ route('kategori-permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Kat. Permintaan</a>
                            <a href="{{ route('master.supir.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Master Supir</a>
                            <a href="{{ route('master.grades.index') }}" class="list-group-item sub-menu-item sidebar-nav-item">Kegawatan</a>
                        </div>
                    </div>

                    <a href="{{ route('users.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> <span>Kelola User</span>
                    </a>
                @endif

                {{-- Promo Card Layout --}}
                <div class="promo-card mt-4">
                    <h6>Menu Pintas</h6>
                    <p>Butuh keluar dari sesi aplikasi?</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="promo-btn">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
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
                    <button class="btn btn-link text-dark p-0 me-3 d-md-none" id="menu-toggle">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    
                    {{-- Search Bar --}}
                    <div class="header-search d-none d-md-flex">
                        <i class="bi bi-search text-muted"></i>
                        <input type="text" id="menuSearchInput" placeholder="Search menu...">
                        <div class="bg-light rounded px-2 text-muted small ms-2 border" style="font-size:0.7rem;">⌘F</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="top-icon-btn"><i class="bi bi-envelope"></i></a>
                    
                    <div class="dropdown">
                        <a href="#" class="top-icon-btn position-relative" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-3" style="width: 300px;">
                            <li class="p-3 border-bottom d-flex justify-content-between">
                                <span class="fw-bold">Notifikasi</span>
                                <span class="badge bg-danger rounded-pill">{{ $notifCount }} H-1</span>
                            </li>
                            <div style="max-height: 250px; overflow-y: auto;" class="py-2 px-2">
                                @forelse($logbookNotifs as $notif)
                                <li>
                                    <a class="dropdown-item rounded-2 p-2 d-flex align-items-start" href="{{ route('logbook.index') }}">
                                        <div class="text-danger me-2 mt-1"><i class="bi bi-circle-fill" style="font-size: 8px;"></i></div>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.85rem;">{{ $notif->judul_acara }}</div>
                                            <small class="text-muted d-block">{{ $notif->deskripsi ?? 'Tidak ada deskripsi' }}</small>
                                            <small class="text-danger" style="font-size: 0.7rem;">Besok - {{ \Carbon\Carbon::parse($notif->tanggal_acara)->format('d M Y') }}</small>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="p-3 text-center text-muted small">Tidak ada agenda H-1 untuk besok.</li>
                                @endforelse
                            </div>
                        </ul>
                    </div>

                    {{-- Profil User --}}
                    <div class="dropdown ms-2">
                        <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown">
                            <div class="text-end d-none d-md-block me-2">
                                <div class="fw-semibold" style="font-size: 0.85rem; line-height: 1;">{{ Auth::user()->name }}</div>
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ Auth::user()->email ?? Auth::user()->role }}</div>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=e5e7eb&color=111827&bold=true" class="rounded-circle" width="38" height="38">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3 rounded-3">
                            <li><a class="dropdown-item py-2 text-muted" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
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
            // Toggle Sidebar
            var toggleBtn = document.getElementById("menu-toggle");
            var overlay = document.getElementById("overlayClick");

            function toggleMenu(e) {
                e.preventDefault();
                document.body.classList.toggle("toggled");
            }

            if(toggleBtn) toggleBtn.addEventListener("click", toggleMenu);
            if(overlay) overlay.addEventListener("click", toggleMenu);

            // Realtime Search Menu
            const searchInput = document.getElementById("menuSearchInput");
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    const filter = this.value.toLowerCase().trim();
                    const menuItems = document.querySelectorAll(".sidebar-nav-item");
                    const groupLabels = document.querySelectorAll(".menu-group-label");

                    if (filter === "") {
                        menuItems.forEach(el => el.style.display = "");
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