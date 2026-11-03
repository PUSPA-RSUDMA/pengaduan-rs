@extends('layouts.admin')
@section('title', 'Dashboard PKRS')
@section('content')

<style>
    body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
    .card { border-radius: 16px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; border: 1px solid rgba(0,0,0,0.02) !important; background-color: #ffffff; transition: 0.2s; }
    .hover-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.06) !important; }
    .card-header { background-color: transparent !important; border-bottom: none !important; padding: 1.5rem 1.5rem 0.5rem 1.5rem !important; }
    .text-title { color: #111827; font-weight: 700; font-size: 1.1rem; }
    .text-subtitle { color: #6b7280; font-weight: 500; font-size: 0.85rem; }
    .icon-box { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; }
    .form-select-custom { border-radius: 20px; border: 1px solid #e5e7eb; font-size: 0.8rem; font-weight: 500; color: #374151; padding: 0.3rem 1rem; }
    .form-select-custom:focus { border-color: #10b981; box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15); }
</style>

{{-- HEADER & FILTER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="m-0 fw-bold text-dark" style="font-size: 1.3rem;">Dashboard Promosi Kesehatan</h4>
        <small class="text-muted">Statistik kegiatan PKRS Rumah Sakit</small>
    </div>
    <form action="{{ route('pkrs.dashboard') }}" method="GET" class="m-0">
        <div class="d-flex align-items-center gap-2">
            <span class="text-subtitle mb-0">Tahun:</span>
            <select name="year" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- REKAP STATISTIK --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <span class="text-subtitle">Total Kegiatan ({{ $selectedYear }})</span>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="icon-box" style="background-color: #eef2ff; color: #6366f1;"><i class="bi bi- megaphone fs-5"></i></div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalKegiatan }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <span class="text-subtitle">Status: Selesai</span>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="icon-box" style="background-color: #e6f7ec; color: #10b981;"><i class="bi bi-check-circle fs-5"></i></div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalSelesai }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <span class="text-subtitle">Status: Proses</span>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="icon-box" style="background-color: #fef3c7; color: #d97706;"><i class="bi bi-arrow-repeat fs-5"></i></div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalProses }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <span class="text-subtitle">Status: Belum</span>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <div class="icon-box" style="background-color: #fff1f2; color: #f43f5e;"><i class="bi bi-x-circle fs-5"></i></div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalBelum }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHART AREA --}}
<div class="row g-4 mb-4">
    {{-- TREN BULANAN (LINE CHART) --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header"><span class="text-title">Tren Kegiatan per Bulan</span></div>
            <div class="card-body"><div style="height: 300px;"><canvas id="trendChart"></canvas></div></div>
        </div>
    </div>

    {{-- CHART OUTPUT (DOUGHNUT) --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><span class="text-title">Distribusi Output</span></div>
            <div class="card-body d-flex justify-content-center"><div style="height: 280px; width: 100%;"><canvas id="outputChart"></canvas></div></div>
        </div>
    </div>

    {{-- CHART REQUEST BY (BAR) --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="text-title">Distribusi Berdasarkan Request By ({{ $selectedYear }})</span></div>
            <div class="card-body"><div style="height: 350px;"><canvas id="requestChart"></canvas></div></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.defaults.font.family = "'Inter', sans-serif";
        const chartColors = ['#10b981', '#3b82f6', '#f43f5e', '#eab308', '#8b5cf6', '#14b8a6', '#f97316'];

        // 1. Line Chart (Bulanan)
        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Kegiatan',
                    data: {!! json_encode($dataBulanan) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3, tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: '#fff'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, suggestedMax: 5 } } }
        });

        // 2. Bar Chart (Request By)
        const reqData = {!! json_encode($requestData) !!};
        new Chart(document.getElementById('requestChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: Object.keys(reqData),
                datasets: [{
                    label: 'Total Request',
                    data: Object.values(reqData),
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // 3. Doughnut Chart (Output)
        const outData = {!! json_encode($outputData) !!};
        new Chart(document.getElementById('outputChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(outData),
                datasets: [{
                    data: Object.values(outData),
                    backgroundColor: chartColors,
                    borderWidth: 0, hoverOffset: 5
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: {size: 11} } } } }
        });
    });
</script>
@endsection