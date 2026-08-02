@extends('layouts.admin')
@section('title', 'Dashboard Utama RSUD')
@section('content')
<style>
    .hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; z-index: 10; }
    .progress-thin { height: 6px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0"><i class="bi bi-grid-1x2-fill text-primary me-2"></i>Ringkasan Eksekutif Layanan</h4>
        <small class="text-muted">Overview seluruh aktivitas layanan publik RSUD</small>
    </div>
    
    <form action="{{ route('utama.dashboard') }}" method="GET" class="m-0">
        <div class="input-group input-group-sm shadow-sm">
            <span class="input-group-text bg-primary text-white border-primary"><i class="bi bi-calendar-event me-1"></i> Tahun</span>
            <select name="year" class="form-select fw-bold border-primary" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>
</div>

{{-- 1. KARTU STATISTIK GABUNGAN --}}
<div class="row g-3 mb-4">
    {{-- Kartu Grand Total --}}
    <div class="col-12 col-md-3">
        <div class="card bg-dark text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 small mb-1 text-uppercase">Total Interaksi</h6>
                    <h2 class="fw-bold mb-0">{{ $grandTotal }}</h2>
                </div>
                <i class="bi bi-activity fs-1 opacity-25"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 p-2 text-center">
                <small>Di Tahun {{ $tahun }}</small>
            </div>
        </div>
    </div>

    {{-- Kartu Pengaduan --}}
    <div class="col-12 col-md-3">
        <div class="card bg-danger text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 small mb-1 text-uppercase">Pengaduan Masuk</h6>
                    <h2 class="fw-bold mb-0">{{ $pengaduanTotal }}</h2>
                </div>
                <i class="bi bi-chat-square-text fs-1 opacity-25"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 p-2 d-flex justify-content-around">
                <small><i class="bi bi-hourglass-split"></i> {{ $pengaduanPending }} Pending</small>
                <small><i class="bi bi-check-circle"></i> {{ $pengaduanSelesai }} Selesai</small>
            </div>
        </div>
    </div>

    {{-- Kartu Permintaan --}}
    <div class="col-12 col-md-3">
        <div class="card bg-info text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 small mb-1 text-uppercase">Permintaan Info</h6>
                    <h2 class="fw-bold mb-0">{{ $permintaanTotal }}</h2>
                </div>
                <i class="bi bi-headset fs-1 opacity-25"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 p-2 d-flex justify-content-around">
                <small><i class="bi bi-whatsapp"></i> {{ $permintaanChat }} Chat</small>
                <small><i class="bi bi-telephone"></i> {{ $permintaanTelfon }} Telfon</small>
            </div>
        </div>
    </div>

    {{-- Kartu LaSehat --}}
    <div class="col-12 col-md-3">
        <div class="card bg-success text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 small mb-1 text-uppercase">Order LaSehat</h6>
                    <h2 class="fw-bold mb-0">{{ $lasehatTotal }}</h2>
                </div>
                <i class="bi bi-ambulance fs-1 opacity-25"></i>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 p-2 text-center">
                <small>Pengantaran Pasien Selesai</small>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK PERBANDINGAN --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <span class="fw-bold text-dark"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Perbandingan Volume Layanan</span>
            </div>
            <div class="card-body">
                <div style="height: 300px; width: 100%;">
                    <canvas id="mainBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <span class="fw-bold text-dark"><i class="bi bi-pie-chart-fill text-warning me-2"></i>Proporsi Layanan</span>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div style="height: 250px; width: 100%;">
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
        const colors = ['#dc3545', '#0dcaf0', '#198754']; // Sesuai warna kartu: Merah, Biru, Hijau

        // BAR CHART
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total',
                    data: dataValues,
                    backgroundColor: colors,
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end', align: 'top', color: 'black', font: { weight: 'bold', size: 14 },
                        formatter: (val) => val > 0 ? val : ''
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [3, 3] }, suggestedMax: Math.max(...dataValues) + 10 },
                    x: { grid: { display: false } }
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
                    borderWidth: 2,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#fff', font: { weight: 'bold', size: 14 },
                        formatter: (val, ctx) => {
                            let sum = ctx.chart._metasets[ctx.datasetIndex].total;
                            let percentage = (val * 100 / sum).toFixed(1) + "%";
                            return val > 0 ? percentage : '';
                        }
                    }
                }
            }
        });
    });
</script>
@endsection