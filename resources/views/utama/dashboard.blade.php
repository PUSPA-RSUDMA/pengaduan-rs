@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('content')
<style>
    /* Mengikuti Gaya Desain Gambar */
    .stat-card {
        background: #ffffff;
        border-radius: 20px;
        border: none;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 25px rgba(0,0,0,0.06); 
    }
    
    .stat-title { font-size: 0.85rem; color: #111827; font-weight: 600; display: flex; align-items: center;}
    .stat-title i { font-size: 0.8rem; color: #9ca3af; margin-left: 6px; }
    
    .btn-details {
        font-size: 0.7rem;
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        color: #6b7280;
        text-decoration: none;
        font-weight: 500;
        background: #fff;
    }
    .btn-details:hover { background: #f9fafb; color: #111827; }

    .stat-number { font-size: 2.2rem; font-weight: 700; color: #111827; line-height: 1; }
    
    .badge-pill-custom {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-green { background: #dcfce7; color: #16a34a; }
    .badge-yellow { background: #fef9c3; color: #ca8a04; }
    .badge-red { background: #fee2e2; color: #dc2626; }
    
    .icon-box-small {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .icon-box-small.green { background: #dcfce7; color: #16a34a; }
    .icon-box-small.yellow { background: #fef9c3; color: #ca8a04; }
    .icon-box-small.red { background: #fee2e2; color: #dc2626; }
    
    .stat-desc { font-size: 0.75rem; color: #6b7280; margin-top: 15px; margin-bottom: 0;}

    /* Styling Chart Card */
    .chart-card {
        background: #ffffff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .chart-card-header { background: transparent; padding: 20px 24px 10px; border-bottom: none; }
    .chart-card-title { font-weight: 600; color: #111827; font-size: 1.1rem; display: flex; align-items: center; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h4 class="fw-bold m-0 text-dark">Ringkasan Eksekutif</h4>
    </div>
    
    <form action="{{ route('utama.dashboard') }}" method="GET" class="m-0">
        <select name="year" class="form-select border-0 shadow-sm fw-bold" style="border-radius: 20px; padding-left: 20px; padding-right: 35px; color: #4b5563; cursor: pointer;" onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

{{-- 1. KARTU STATISTIK GABUNGAN SESUAI DESAIN UI --}}
<div class="row g-3 mb-4">
    {{-- Kartu Total Interaksi (Tema Hijau) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Total Interaksi <i class="bi bi-info-circle"></i></span>
                <a href="#" class="btn-details">See Details</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small green"><i class="bi bi-people-fill"></i></div>
                <h3 class="stat-number mb-0">{{ $grandTotal }}</h3>
                <span class="badge-pill-custom badge-green"><i class="bi bi-arrow-up-right"></i> 100%</span>
            </div>
            <p class="stat-desc">Aktivitas layanan publik RSUD tumbuh dalam {{ $tahun }}</p>
        </div>
    </div>

    {{-- Kartu Pengaduan (Tema Hijau Muda) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Pengaduan Masuk <i class="bi bi-info-circle"></i></span>
                <a href="#" class="btn-details">See Details</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small green bg-opacity-50"><i class="bi bi-chat-square-text-fill"></i></div>
                <h3 class="stat-number mb-0">{{ sprintf("%02d", $pengaduanTotal) }}</h3>
                <span class="badge-pill-custom badge-green bg-opacity-50" style="color: #059669;"><i class="bi bi-arrow-up-right"></i> {{ $pengaduanTotal > 0 ? 'Aktif' : '0%' }}</span>
            </div>
            <p class="stat-desc">{{ $pengaduanSelesai }} selesai ditangani, menyisakan {{ $pengaduanPending }} pending.</p>
        </div>
    </div>

    {{-- Kartu Permintaan (Tema Kuning) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Permintaan Info <i class="bi bi-info-circle"></i></span>
                <a href="#" class="btn-details">See Details</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small yellow"><i class="bi bi-headset"></i></div>
                <h3 class="stat-number mb-0">{{ sprintf("%02d", $permintaanTotal) }}</h3>
                <span class="badge-pill-custom badge-yellow"><i class="bi bi-arrow-up-right"></i> {{ $permintaanTotal > 0 ? 'Proses' : '0%' }}</span>
            </div>
            <p class="stat-desc">{{ $permintaanChat }} via Chat dan {{ $permintaanTelfon }} via Telepon masuk.</p>
        </div>
    </div>

    {{-- Kartu LaSehat (Tema Merah) --}}
    <div class="col-12 col-md-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Order LaSehat <i class="bi bi-info-circle"></i></span>
                <a href="#" class="btn-details">See Details</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small red"><i class="bi bi-ambulance"></i></div>
                <h3 class="stat-number mb-0">{{ sprintf("%02d", $lasehatTotal) }}</h3>
                <span class="badge-pill-custom badge-red"><i class="bi bi-arrow-down-right"></i> Selesai</span>
            </div>
            <p class="stat-desc">Total layanan pengantaran pasien selesai dilakukan.</p>
        </div>
    </div>
</div>

{{-- 2. GRAFIK PERBANDINGAN --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-card-header d-flex justify-content-between align-items-center">
                <span class="chart-card-title">Daily Attendance Statistic <i class="bi bi-info-circle ms-2" style="font-size: 0.9rem; color: #9ca3af; font-weight: normal;"></i></span>
                
                {{-- Legend Custom Ala Gambar --}}
                <div class="d-flex gap-3" style="font-size: 0.75rem; font-weight: 500; color:#6b7280;">
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981; margin-right:4px;"></span> Pengaduan</div>
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#eab308; margin-right:4px;"></span> Permintaan</div>
                    <div><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#ef4444; margin-right:4px;"></span> LaSehat</div>
                </div>
            </div>
            <div class="card-body p-4">
                <div style="height: 320px; width: 100%;">
                    <canvas id="mainBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <span class="chart-card-title">Proporsi Layanan <i class="bi bi-info-circle ms-2" style="font-size: 0.9rem; color: #9ca3af; font-weight: normal;"></i></span>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center p-4">
                <div style="height: 280px; width: 100%;">
                    <canvas id="mainDoughnutChart"></canvas>
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
        
        // Skema warna (Hijau, Kuning, Merah) disesuaikan persis seperti pada referensi UI
        const colors = ['#10b981', '#eab308', '#ef4444']; 
        // Latar belakang bar tidak aktif bergaya abu-abu tipis
        const bgColors = ['#e5e7eb', '#e5e7eb', '#e5e7eb'];

        // BAR CHART
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Volume',
                    data: dataValues,
                    backgroundColor: colors,
                    borderRadius: 8,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { display: false } // Dihilangkan agar grafik terlihat clean sesuai gambar
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f3f4f6', drawBorder: false }, 
                        ticks: { color: '#9ca3af', font: {size: 11} },
                        suggestedMax: Math.max(...dataValues) + 10 
                    },
                    x: { 
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#9ca3af', font: {size: 11} }
                    }
                }
            }
        });

        // DOUGHNUT CHART
        new Chart(document.getElementById('mainDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: {family: "'Poppins', sans-serif", size: 12, color: '#6b7280'} } },
                    datalabels: {
                        color: '#fff', font: { weight: '600', size: 12 },
                        formatter: (val, ctx) => {
                            let sum = ctx.chart._metasets[ctx.datasetIndex].total;
                            let percentage = (val * 100 / sum).toFixed(0) + "%";
                            return val > 0 ? percentage : '';
                        }
                    }
                }
            }
        });
    });
</script>
@endsection