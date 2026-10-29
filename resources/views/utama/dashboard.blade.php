@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('content')
<style>
    /* Styling Khusus Panel Dashboard */
    .dashboard-panel { 
        background: var(--bg-surface); 
        border-radius: 16px; 
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .panel-header {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
    }
    .panel-val { font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 5px; }
    .panel-desc { font-size: 0.75rem; color: var(--text-secondary); }
    
    /* Box Statistik Kecil (Bagian Bawah Card) */
    .inner-stat-box {
        background: var(--bg-inner);
        border-radius: 10px;
        padding: 12px;
        flex: 1;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.02);
    }
    .inner-stat-label { font-size: 0.65rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
    .inner-stat-value { font-size: 1rem; font-weight: 600; color: #fff; margin-top: 4px; }
    .inner-stat-box.active {
        background: rgba(108, 56, 255, 0.1);
        border-bottom: 3px solid #9B51E0;
    }
    
    /* Progress Bar Mirip Image */
    .progress-neon { height: 6px; background-color: var(--bg-inner); border-radius: 4px; margin-top: 15px; }
    .progress-bar-neon { border-radius: 4px; }
    
    /* Select Tahun */
    .year-select { background: var(--bg-inner); color: #fff; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.8rem; }
    .year-select:focus { outline: none; border-color: #9B51E0; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0 text-white">Ringkasan Layanan Publik</h4>
        <small class="text-secondary">Overview seluruh aktivitas layanan publik RSUD</small>
    </div>
    
    <form action="{{ route('utama.dashboard') }}" method="GET" class="m-0">
        <select name="year" class="year-select px-3 py-2 fw-medium shadow-sm" onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

{{-- 1. KARTU STATISTIK GRID --}}
<div class="row g-4 mb-4">
    
    {{-- Kartu Grand Total (Theme: Info/Blue) --}}
    <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-panel p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="panel-header">
                    Total Interaksi <i class="bi bi-chevron-right text-secondary" style="font-size: 0.75rem;"></i>
                </div>
                <div class="panel-val">{{ $grandTotal }}</div>
                <div class="panel-desc">Total layanan tercatat tahun ini</div>
                
                <div class="progress-neon">
                    <div class="progress-bar-neon bg-primary h-100" style="width: 100%;"></div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <div class="inner-stat-box active" style="border-bottom-color: #0d6efd;">
                    <div class="inner-stat-label">Tahun</div>
                    <div class="inner-stat-value">{{ $tahun }}</div>
                </div>
                <div class="inner-stat-box">
                    <div class="inner-stat-label">Status</div>
                    <div class="inner-stat-value text-success">Aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Pengaduan (Theme: Pink/Danger) --}}
    <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-panel p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="panel-header">
                    Pengaduan <i class="bi bi-chevron-right text-secondary" style="font-size: 0.75rem;"></i>
                </div>
                <div class="panel-val">{{ $pengaduanTotal }}</div>
                <div class="panel-desc">Keluhan & Masukan Masyarakat</div>
                
                <div class="progress-neon">
                    @php $persenPengaduan = $pengaduanTotal > 0 ? ($pengaduanSelesai / $pengaduanTotal) * 100 : 0; @endphp
                    <div class="progress-bar-neon h-100" style="width: {{ $persenPengaduan }}%; background: linear-gradient(90deg, #ec4899, #f43f5e);"></div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <div class="inner-stat-box">
                    <div class="inner-stat-label">Pending</div>
                    <div class="inner-stat-value">{{ $pengaduanPending }}</div>
                </div>
                <div class="inner-stat-box active" style="border-bottom-color: #ec4899; background: rgba(236, 72, 153, 0.1);">
                    <div class="inner-stat-label">Selesai</div>
                    <div class="inner-stat-value">{{ $pengaduanSelesai }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Permintaan (Theme: Teal/Info) --}}
    <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-panel p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="panel-header">
                    Permintaan <i class="bi bi-chevron-right text-secondary" style="font-size: 0.75rem;"></i>
                </div>
                <div class="panel-val">{{ $permintaanTotal }}</div>
                <div class="panel-desc">Informasi Layanan RSUD</div>
                
                <div class="progress-neon">
                    <div class="progress-bar-neon h-100" style="width: 75%; background: linear-gradient(90deg, #14b8a6, #06b6d4);"></div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <div class="inner-stat-box active" style="border-bottom-color: #06b6d4; background: rgba(6, 182, 212, 0.1);">
                    <div class="inner-stat-label">Chat / WA</div>
                    <div class="inner-stat-value">{{ $permintaanChat }}</div>
                </div>
                <div class="inner-stat-box">
                    <div class="inner-stat-label">Telfon</div>
                    <div class="inner-stat-value">{{ $permintaanTelfon }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu LaSehat (Theme: Yellow/Success) --}}
    <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-panel p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="panel-header">
                    LaSehat <i class="bi bi-chevron-right text-secondary" style="font-size: 0.75rem;"></i>
                </div>
                <div class="panel-val">{{ $lasehatTotal }}</div>
                <div class="panel-desc">Order Pengantaran Pasien</div>
                
                <div class="progress-neon">
                    <div class="progress-bar-neon h-100" style="width: 100%; background: linear-gradient(90deg, #eab308, #f59e0b);"></div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <div class="inner-stat-box w-100 active" style="border-bottom-color: #eab308; background: rgba(234, 179, 8, 0.1);">
                    <div class="inner-stat-label">Status Delivery</div>
                    <div class="inner-stat-value text-warning">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK PERBANDINGAN --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="dashboard-panel h-100">
            <div class="p-4 border-bottom" style="border-color: var(--border-color)!important;">
                <span class="fw-semibold text-white">Perbandingan Volume Layanan</span>
            </div>
            <div class="p-4">
                <div style="height: 300px; width: 100%;">
                    <canvas id="mainBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dashboard-panel h-100">
            <div class="p-4 border-bottom" style="border-color: var(--border-color)!important;">
                <span class="fw-semibold text-white">Proporsi Layanan</span>
            </div>
            <div class="p-4 d-flex justify-content-center align-items-center">
                <div style="height: 250px; width: 100%; position: relative;">
                    <canvas id="mainDoughnutChart"></canvas>
                    {{-- Indikator Persentase Tengah (Sesuai Konsep Gambar) --}}
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <span class="d-block fw-bold text-white fs-4">{{ $grandTotal }}</span>
                        <span class="text-secondary" style="font-size: 0.65rem;">Total Data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.1.0"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.register(ChartDataLabels);

        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartData) !!};
        
        // Colors corresponding to the glowing neon accents
        const colors = ['#ec4899', '#06b6d4', '#eab308']; 

        // Global Chart Defaults untuk Dark Mode
        Chart.defaults.color = '#8A869D';
        Chart.defaults.font.family = "'Poppins', sans-serif";

        // BAR CHART
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Layanan',
                    data: dataValues,
                    backgroundColor: colors,
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end', align: 'top', color: '#FFFFFF', font: { weight: 'bold', size: 12 },
                        formatter: (val) => val > 0 ? val : ''
                    },
                    tooltip: { backgroundColor: '#211D32', titleColor: '#fff', bodyColor: '#fff', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1 }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(255, 255, 255, 0.05)', borderDash: [5, 5] }, 
                        suggestedMax: Math.max(...dataValues) + 10 
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        // DOUGHNUT CHART (Mirip Grafik Lingkaran pada Gambar Referensi)
        new Chart(document.getElementById('mainDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 4,
                    spacing: 5, // Memberikan jarak antar potongan lingkaran
                    borderRadius: 10 // Ujung potongan melingkar (hanya disupport ChartJS 3.x+ / 4.x)
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '80%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 20, color: '#8A869D' } },
                    datalabels: { display: false }, // Sembunyikan label di dalam chart, fokus ke visual cincin
                    tooltip: { backgroundColor: '#211D32', titleColor: '#fff', bodyColor: '#fff' }
                }
            }
        });
    });
</script>
@endsection