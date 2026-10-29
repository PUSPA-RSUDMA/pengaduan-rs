@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('content')
<style>
    .stat-card {
        border-radius: var(--radius-lg);
        padding: 24px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
    
    .stat-card.primary { background-color: var(--primary-green); color: white; border: none; }
    .stat-card.secondary { background-color: #ffffff; color: var(--text-dark); }
    
    .card-number { font-size: 2.5rem; font-weight: 700; margin: 10px 0; line-height: 1; }
    .card-title-text { font-size: 0.95rem; font-weight: 500; }
    
    .arrow-icon {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .primary .arrow-icon { background: rgba(255,255,255,0.15); color: white; }
    .secondary .arrow-icon { background: transparent; border: 1px solid #e5e7eb; color: var(--text-dark); }
    
    .trend-text { font-size: 0.75rem; display: flex; align-items: center; gap: 6px; }
    .trend-box { 
        padding: 2px 6px; 
        border-radius: 4px; 
        font-weight: 600; 
    }
    .primary .trend-box { background: rgba(255,255,255,0.2); color: white; }
    .secondary .trend-box { background: #f3f4f6; color: var(--text-muted); }

    .header-title-area h2 { font-weight: 600; margin-bottom: 4px; letter-spacing: -0.5px; }
    .header-title-area p { color: var(--text-muted); font-size: 0.9rem; }

    /* Custom Buttons Header */
    .btn-custom-outline {
        border: 1px solid var(--border-color);
        background: white;
        color: var(--text-dark);
        border-radius: 20px;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    
    /* Panel Cards */
    .panel-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
</style>

{{-- HEADER DASHBOARD --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 header-title-area">
    <div>
        <h2>Dashboard</h2>
        <p>Plan, prioritize, and accomplish your tasks with ease.</p>
    </div>
    
    <div class="d-flex gap-3 mt-3 mt-md-0">
        {{-- Tombol Filter Tahun Disesuaikan dengan Gaya "Import Data" di Desain --}}
        <form action="{{ route('utama.dashboard') }}" method="GET" class="m-0">
            <select name="year" class="form-select btn-custom-outline" onchange="this.form.submit()" style="cursor: pointer;">
                @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
</div>

{{-- 1. ROW STATISTIK (4 KARTU) --}}
<div class="row g-3 mb-4">
    {{-- Kartu 1: Grand Total (Hijau) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card primary h-100 position-relative">
            <div class="d-flex justify-content-between align-items-start">
                <span class="card-title-text">Total Layanan</span>
                <div class="arrow-icon"><i class="bi bi-arrow-up-right"></i></div>
            </div>
            <div class="card-number">{{ $grandTotal }}</div>
            <div class="trend-text mt-4">
                <span class="trend-box"><i class="bi bi-activity"></i></span> Total Interaksi {{ $tahun }}
            </div>
        </div>
    </div>

    {{-- Kartu 2: Pengaduan (Putih) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card secondary h-100 position-relative">
            <div class="d-flex justify-content-between align-items-start">
                <span class="card-title-text">Total Pengaduan</span>
                <div class="arrow-icon"><i class="bi bi-arrow-up-right"></i></div>
            </div>
            <div class="card-number">{{ $pengaduanTotal }}</div>
            <div class="trend-text mt-4 text-muted">
                <span class="trend-box">{{ $pengaduanPending }}</span> Pending &nbsp;|&nbsp; 
                <span class="trend-box">{{ $pengaduanSelesai }}</span> Selesai
            </div>
        </div>
    </div>

    {{-- Kartu 3: Permintaan (Putih) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card secondary h-100 position-relative">
            <div class="d-flex justify-content-between align-items-start">
                <span class="card-title-text">Permintaan Info</span>
                <div class="arrow-icon"><i class="bi bi-arrow-up-right"></i></div>
            </div>
            <div class="card-number">{{ $permintaanTotal }}</div>
            <div class="trend-text mt-4 text-muted">
                <span class="trend-box"><i class="bi bi-whatsapp"></i> {{ $permintaanChat }}</span> Chat &nbsp; 
                <span class="trend-box"><i class="bi bi-telephone"></i> {{ $permintaanTelfon }}</span> Telp
            </div>
        </div>
    </div>

    {{-- Kartu 4: LaSehat (Putih) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card secondary h-100 position-relative">
            <div class="d-flex justify-content-between align-items-start">
                <span class="card-title-text">Order LaSehat</span>
                <div class="arrow-icon"><i class="bi bi-arrow-up-right"></i></div>
            </div>
            <div class="card-number">{{ $lasehatTotal }}</div>
            <div class="trend-text mt-4 text-muted">
                <span class="trend-box"><i class="bi bi-ambulance"></i></span> Pengantaran Pasien
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK PERBANDINGAN --}}
<div class="row g-4">
    {{-- Bar Chart --}}
    <div class="col-lg-8">
        <div class="panel-card h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-semibold m-0">Project Analytics <span class="text-muted fw-normal" style="font-size: 0.8rem;">(Volume Layanan)</span></h6>
            </div>
            <div style="height: 300px; width: 100%;">
                <canvas id="mainBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Doughnut Chart (Berbentuk Setengah Lingkaran Sesuai Desain) --}}
    <div class="col-lg-4">
        <div class="panel-card h-100 p-4 d-flex flex-column">
            <h6 class="fw-semibold mb-4">Project Progress</h6>
            <div class="flex-grow-1 d-flex justify-content-center align-items-center position-relative">
                <div style="height: 220px; width: 100%;">
                    <canvas id="mainDoughnutChart"></canvas>
                </div>
                {{-- Label di Tengah Lingkaran --}}
                <div class="position-absolute text-center" style="top: 55%;">
                    <h3 class="fw-bold mb-0" id="totalDoughnutVal">0</h3>
                    <small class="text-muted">Total Layanan</small>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-3 mt-2 text-muted" style="font-size: 0.75rem;">
                <span><i class="bi bi-circle-fill text-success"></i> Pengaduan</span>
                <span><i class="bi bi-circle-fill" style="color: #164e32;"></i> Permintaan</span>
                <span><i class="bi bi-circle-fill text-secondary"></i> LaSehat</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Data dari Controller
        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartData) !!};
        
        // Warna palette dari desain (Hijau tua, Hijau muda, Abu-abu strip)
        const colors = ['#4ade80', '#164e32', '#e5e7eb']; 

        // BAR CHART
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total',
                    data: dataValues,
                    backgroundColor: colors,
                    borderRadius: 100, // Membuat ujung bar melengkung seperti pil
                    borderSkipped: false,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        display: false, // Menghilangkan garis Y seperti pada desain
                        beginAtZero: true 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { weight: '500' } }
                    }
                }
            }
        });

        // DOUGHNUT CHART (SETENGAH LINGKARAN)
        let totalVal = dataValues.reduce((a, b) => a + b, 0);
        document.getElementById('totalDoughnutVal').innerText = totalVal;

        new Chart(document.getElementById('mainDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '75%',
                circumference: 180, // Membuatnya setengah lingkaran
                rotation: -90,      // Memutar posisinya agar pas di atas
                plugins: {
                    legend: { display: false }, // Legend kustom dibuat di HTML
                }
            }
        });
    });
</script>
@endsection