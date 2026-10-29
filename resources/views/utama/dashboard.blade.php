@extends('layouts.admin')
@section('title', 'Overview')
@section('content')
<style>
    /* Styling Card Summary menyerupai referensi gambar */
    .stat-card {
        border-radius: 20px;
        background: #ffffff;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: transform 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
    
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    /* Warna ikon ungu dan biru */
    .icon-purple { background: rgba(85, 66, 246, 0.1); color: #5542F6; }
    .icon-blue { background: rgba(50, 215, 243, 0.1); color: #32D7F3; }
    
    .chart-card {
        border-radius: 24px;
        background: #ffffff;
        border: none;
        box-shadow: 0 4px 25px rgba(0,0,0,0.03);
        padding: 25px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-muted" style="font-size: 0.9rem;">
        Menampilkan data ringkasan layanan
    </div>
    
    <form action="{{ route('utama.dashboard') }}" method="GET" class="m-0">
        <select name="year" class="form-select border-0 shadow-sm" style="border-radius: 15px; font-weight: 500; color: #2B3674;" onchange="this.form.submit()">
            <option disabled>Show by years</option>
            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

{{-- 1. KARTU STATISTIK (Disesuaikan jadi warna putih bersih) --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon icon-purple">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Total Interaksi</h6>
                <h3 class="fw-bold mb-0" style="color: #2B3674;">{{ $grandTotal }}</h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon icon-blue">
                    <i class="bi bi-chat-square-text"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Pengaduan Masuk</h6>
                <div class="d-flex align-items-end justify-content-between">
                    <h3 class="fw-bold mb-0" style="color: #2B3674;">{{ $pengaduanTotal }}</h3>
                    <small class="text-success fw-bold" style="font-size: 0.75rem;">{{ $pengaduanSelesai }} Selesai</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon icon-purple">
                    <i class="bi bi-headset"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Permintaan Info</h6>
                <div class="d-flex align-items-end justify-content-between">
                    <h3 class="fw-bold mb-0" style="color: #2B3674;">{{ $permintaanTotal }}</h3>
                    <small class="text-primary fw-bold" style="font-size: 0.75rem;">{{ $permintaanChat }} via WA</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon icon-blue">
                    <i class="bi bi-car-front-fill"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-1" style="font-size: 0.8rem; font-weight: 500;">Order LaSehat</h6>
                <h3 class="fw-bold mb-0" style="color: #2B3674;">{{ $lasehatTotal }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK (Warna disesuaikan ke Ungu & Biru) --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0" style="color: #2B3674;">Analytics Data</h6>
            </div>
            <div style="height: 300px; width: 100%;">
                <canvas id="mainBarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card h-100">
            <h6 class="fw-bold mb-4" style="color: #2B3674;">Proporsi Layanan</h6>
            <div class="d-flex justify-content-center align-items-center" style="height: 250px;">
                <canvas id="mainDoughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.1.0"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Hapus DataLabels secara default agar chart terlihat lebih clean seperti gambar referensi
        // Chart.register(ChartDataLabels); 

        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartData) !!};
        
        // Palette Warna sesuai gambar (Ungu tua, Biru terang, Abu-abu lembut)
        const colors = ['#5542F6', '#32D7F3', '#E2E8F0']; 

        // BAR CHART (dibuat ramping dan ujung membulat)
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total',
                    data: dataValues,
                    backgroundColor: colors,
                    borderRadius: 10, // Lengkungan di ujung bar chart
                    barThickness: 20, // Mengatur lebar bar agar tidak terlalu gemuk
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
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#f1f1f1' }, 
                        border: { display: false },
                        ticks: { color: '#A3AED0', font: { size: 11 } }
                    },
                    x: { 
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#A3AED0', font: { size: 11 } }
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
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '75%', // Lubang tengah yang besar
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            color: '#A3AED0',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection