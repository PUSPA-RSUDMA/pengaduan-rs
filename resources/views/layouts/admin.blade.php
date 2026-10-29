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
            --primary-green: #0fa968;
            --bg-body: #f3f5f9;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-body); 
            overflow-x: hidden; 
            color: var(--text-dark);
        }
        
        /* === SIDEBAR MODERN === */
        #sidebar-wrapper {
            width: 280px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
        }

        #sidebar-wrapper::-webkit-scrollbar { width: 5px; }
        #sidebar-wrapper::-webkit-scrollbar-track { background: transparent; }
        #sidebar-wrapper::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        /* === KONTEN UTAMA === */
        #page-content-wrapper {
            width: calc(100% - 280px);
            margin-left: 280px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* === HEADER / NAVBAR === */
        .main-header {
            background: transparent;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .top-search-bar {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 50rem;
            padding: 8px 20px;
            width: 400px;
            display: flex;
            align-items: center;
        }
        
        .top-search-bar input {
            border: none;
            outline: none;
            box-shadow: none;
            width: 100%;
            background: transparent;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .top-icon-btn {
            width: 45px;
            height: 45px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            text-decoration: none;
            transition: 0.2s;
        }
        .top-icon-btn:hover { background: #f9fafb; }

        /* === LOGIKA TOGGLE === */
        body.toggled #sidebar-wrapper { margin-left: -280px; }
        body.toggled #page-content-wrapper { width: 100%; margin-left: 0; }

        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -280px; }
            #page-content-wrapper { width: 100%; margin-left: 0; }
            body.toggled #sidebar-wrapper { margin-left: 0; }
            .top-search-bar { display: none; }
        }

        /* === STYLING MENU & SUBMENU === */
        .sidebar-header { padding: 30px 25px 20px; }
        .sidebar-label { padding: 15px 30px 5px; font-size: 0.75rem; text-transform: capitalize; color: var(--text-dark); font-weight: 600; }
        
        .list-group-item { 
            background: transparent; 
            color: var(--text-muted); 
            border: none; 
            padding: 12px 20px; 
            margin: 4px 20px; 
            border-radius: 50rem; /* Pill shape */
            transition: all 0.25s ease; 
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
        }
        
        .list-group-item i { font-size: 1.1rem; transition: 0.3s; color: var(--text-dark); }

        .list-group-item:hover { 
            background: #f0fdf4; 
            color: var(--primary-green); 
        }
        .list-group-item:hover i { color: var(--primary-green); }
        
        .list-group-item.active { 
            background: var(--primary-green) !important; 
            color: #ffffff !important; 
            box-shadow: 0 4px 15px rgba(15, 169, 104, 0.3);
        }
        .list-group-item.active i { color: #ffffff !important; }

        .sub-menu-wrapper { margin: 2px 20px; }
        .sub-menu-item { padding: 10px 20px 10px 45px !important; font-size: 0.85rem !important; margin: 2px 0 !important; }

        .menu-arrow { transition: transform 0.3s ease; font-size: 0.75rem !important; margin-left: auto; }
        .collapsed .menu-arrow { transform: rotate(-90deg); }

        /* Animation */
        @keyframes ring {
            0% { transform: rotate(0); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(15deg); }
            40% { transform: rotate(-10deg); }
            50% { transform: rotate(0); }
            100% { transform: rotate(0); }
        }
        .bell-ring { animation: ring 2s ease infinite; display: inline-block; transform-origin: top center; color: #ef4444; }
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
            <div class="sidebar-header d-flex align-items-center">
                <i class="bi bi-hospital-fill fs-2 me-2" style="color: var(--primary-green);"></i>
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">IPP-RSUD</h4>
            </div>
            
            <div class="sidebar-label menu-group-label mt-2">Home</div>
            <div class="list-group list-group-flush pb-4" id="sidebarMenuList">
                @if(auth()->user()->role == 'admin')
                    <a href="{{ route('utama.dashboard') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('utama.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill me-3"></i> <span>Dashboard</span>
                    </a>
                @endif
                
                {{-- KELOMPOK MENU: LAYANAN PUBLIK --}}
                <div class="sidebar-label menu-group-label mt-2">Layanan Publik</div>

                <a href="#submenuPengaduan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex align-items-center {{ request()->routeIs('dashboard', 'complaints.*') ? '' : 'collapsed' }}">
                    <i class="bi bi-chat-square-text me-3"></i> <span>Pengaduan</span>
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

                <a href="#submenuPermintaan" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex align-items-center {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-informasi.*', 'permohonan-uhc.*') ? '' : 'collapsed' }}">
                    <i class="bi bi-file-earmark-medical me-3"></i> <span>Layanan Informasi</span>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('dashboard.permintaan', 'permintaan.*', 'permohonan-informasi.*', 'permohonan-uhc.*') ? 'show' : '' }}" id="submenuPermintaan">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('dashboard.permintaan') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('dashboard.permintaan') ? 'active' : '' }}">Dashboard Info</a>
                        @endif
                        <a href="{{ route('permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permintaan.index') ? 'active' : '' }}">Data Permintaan</a>
                        <a href="{{ route('permohonan-informasi.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-informasi.*') ? 'active' : '' }}">Permohonan Informasi</a>
                        <a href="{{ route('permohonan-uhc.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('permohonan-uhc.*') ? 'active' : '' }}">Peralihan UHC</a>
                    </div>
                </div>

                <a href="#submenuLasehat" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex align-items-center {{ request()->routeIs('lasehat.*') ? '' : 'collapsed' }}">
                    <i class="bi bi-car-front me-3"></i> <span>Mobil LaSehat</span>
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('lasehat.*') ? 'show' : '' }}" id="submenuLasehat">
                    <div class="sub-menu-wrapper">
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('lasehat.dashboard') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.dashboard') ? 'active' : '' }}">Dashboard LaSehat</a>
                        @endif
                        <a href="{{ route('lasehat.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('lasehat.index') ? 'active' : '' }}">Data Mobil LaSehat</a>
                    </div>
                </div>

                <div class="sidebar-label menu-group-label mt-2">Agenda & Integrasi</div>

                <a href="#submenuLogbook" data-bs-toggle="collapse" 
                   class="list-group-item sidebar-nav-toggle d-flex align-items-center {{ request()->routeIs('logbook.*', 'whatsapp.*') ? '' : 'collapsed' }}">
                    <i class="bi bi-calendar-event me-3"></i> <span>Logbook & WA</span>
                    @if($notifCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2" style="font-size: 0.65rem;">{{ $notifCount }}</span>
                    @endif
                    <i class="bi bi-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('logbook.*', 'whatsapp.*') ? 'show' : '' }}" id="submenuLogbook">
                    <div class="sub-menu-wrapper">
                        <a href="{{ route('logbook.index') }}" class="list-group-item sub-menu-item sidebar-nav-item d-flex justify-content-between align-items-center {{ request()->routeIs('logbook.*') ? 'active' : '' }}">
                            <span>Kalender Logbook</span>
                            @if($notifCount > 0) <span class="badge bg-danger rounded-pill">{{ $notifCount }}</span> @endif
                        </a>
                        <a href="{{ route('whatsapp.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">Koneksi WhatsApp</a>
                    </div>
                </div>

                <a href="{{ route('bpjs.index') }}" class="list-group-item sidebar-nav-item {{ request()->routeIs('bpjs.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front me-3"></i> <span>Cari Kepesertaan</span>
                </a>

                @if(auth()->user()->role == 'admin')
                    <div class="sidebar-label menu-group-label mt-2">Pengaturan Admin</div>
                    <a href="#submenuMaster" data-bs-toggle="collapse" 
                        class="list-group-item sidebar-nav-toggle d-flex align-items-center {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*') ? '' : 'collapsed' }}">
                        <i class="bi bi-database me-3"></i> <span>Data Master</span>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('master.*', 'sources.*', 'kategori-keluhan.*', 'kategori-permintaan.*') ? 'show' : '' }}" id="submenuMaster">
                        <div class="sub-menu-wrapper">
                            <a href="{{ route('master.units.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.units.*') ? 'active' : '' }}">Unit Tujuan</a>
                            <a href="{{ route('sources.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('sources.*') ? 'active' : '' }}">Media Pengaduan</a>
                            <a href="{{ route('master.reporters.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.reporters.*') ? 'active' : '' }}">Unit Pelapor</a>
                            <a href="{{ route('kategori-keluhan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('kategori-keluhan.*') ? 'active' : '' }}">Kategori Keluhan</a>
                            <a href="{{ route('kategori-permintaan.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('kategori-permintaan.*') ? 'active' : '' }}">Kategori Permintaan</a>
                            <a href="{{ route('master.supir.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.supir.*') ? 'active' : '' }}">Master Supir</a>
                            <a href="{{ route('master.grades.index') }}" class="list-group-item sub-menu-item sidebar-nav-item {{ request()->routeIs('master.grades.*') ? 'active' : '' }}">Kegawatan</a>
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
                        <button type="submit" class="btn btn-success w-100 btn-sm rounded-pill shadow-sm">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- KONTEN HALAMAN --}}
        <div id="page-content-wrapper">
            
            {{-- HEADER NAVBAR SESUAI DESAIN --}}
            <nav class="main-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" id="menu-toggle" style="width: 40px; height: 40px; background:#fff;">
                        <i class="bi bi-list fs-5 text-dark"></i>
                    </button>
                    
                    {{-- Search bar mirip desain referensi --}}
                    <div class="top-search-bar">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" id="menuSearchInput" placeholder="Search anything Here..." autocomplete="off">
                    </div>
                </div>
                
                {{-- KANAN: Ikon Lonceng, Pesan, Profil --}}
                <div class="d-flex align-items-center gap-3">
                    {{-- DROPDOWN NOTIFIKASI --}}
                    <div class="dropdown">
                        <a href="#" class="top-icon-btn position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5 {{ $notifCount > 0 ? 'bell-ring' : '' }}"></i>
                            @if($notifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger" style="width: 8px; height: 8px; padding: 0;"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="width: 320px; border-radius: 16px;">
                            <li class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold mb-0">Pengingat Logbook</span>
                                <span class="badge bg-danger rounded-pill">{{ $notifCount }} H-1</span>
                            </li>
                            <div style="max-height: 280px; overflow-y: auto;" class="py-2">
                                @forelse($logbookNotifs as $notif)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start py-2" href="{{ route('logbook.index') }}">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3 mt-1" style="width: 35px; height: 35px; flex-shrink: 0;">
                                            <i class="bi bi-calendar-check-fill"></i>
                                        </div>
                                        <div style="overflow: hidden;">
                                            <div class="fw-semibold text-truncate text-dark" style="font-size: 0.85rem;">{{ $notif->judul_acara }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ $notif->deskripsi ?? 'Tidak ada deskripsi' }}</div>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="p-4 text-center">
                                    <small class="text-muted fw-medium">Tidak ada agenda H-1 untuk besok.</small>
                                </li>
                                @endforelse
                            </div>
                        </ul>
                    </div>

                    {{-- Pesan Icon --}}
                    <a href="#" class="top-icon-btn">
                        <i class="bi bi-chat-dots fs-5"></i>
                    </a>

                    {{-- DROPDOWN PROFIL --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="border: 2px solid #fff; border-radius: 50%; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0fa968&color=fff&bold=true" class="rounded-circle" width="45" height="45">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius: 16px;" aria-labelledby="dropdownUser1">
                            <li><h6 class="dropdown-header text-dark fw-bold">Halo, {{ Auth::user()->name }}!</h6></li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear me-2"></i> Profil & Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold py-2"><i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            {{-- ISI KONTEN --}}
            <div class="container-fluid px-4 pb-4">
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

            // Realtime Search (Berfungsi pada Sidebar Item)
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