@extends('layouts.admin')

@section('title', 'Dashboard Monitoring')

@section('content')

<style>
    /* Efek Hover Kartu */
    .hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; z-index: 10; }
</style>

{{-- 1. KARTU STATISTIK --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card bg-primary text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-white-50 small mb-1 text-uppercase">Total Masuk</h6><h2 class="fw-bold mb-0">{{ $total }}</h2></div>
                <i class="bi bi-inbox-fill fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-danger text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-white-50 small mb-1 text-uppercase">Pending</h6><h2 class="fw-bold mb-0">{{ $pending }}</h2></div>
                <i class="bi bi-hourglass-top fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-warning text-dark border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-dark-50 small mb-1 text-uppercase">Sedang Proses</h6><h2 class="fw-bold mb-0">{{ $process ?? 0 }}</h2></div>
                <i class="bi bi-gear-wide-connected fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-success text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-white-50 small mb-1 text-uppercase">Selesai</h6><h2 class="fw-bold mb-0">{{ $done }}</h2></div>
                <i class="bi bi-check-circle-fill fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK --}}
<div class="row">
    
    {{-- CHART BULANAN (KIRI) --}}
    <div class="col-lg-6 col-12 mb-4"> 
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i> Statistik Bulanan</span>
                
                <form action="{{ route('dashboard') }}" method="GET">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <select name="year" class="form-select form-select-sm fw-bold border-primary" onchange="this.form.submit()" style="width: auto;">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART TREN (KANAN) --}}
    <div class="col-lg-6 col-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 border-bottom-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-success"><i class="bi bi-graph-up-arrow me-2"></i> Tren Tahunan</span>
                </div>
                
                {{-- FORM FILTER TREN --}}
                <form action="{{ route('dashboard') }}" method="GET" id="formTren">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    
                    <div class="input-group input-group-sm">
                        <select name="start_year" id="start_year" class="form-select" onchange="validateAndSubmit('start')">
                            @foreach($availableYears as $y) 
                                <option value="{{ $y }}" {{ $startYear == $y ? 'selected' : '' }}>{{ $y }}</option> 
                            @endforeach
                        </select>
                        
                        <span class="input-group-text bg-light text-muted">s/d</span>
                        
                        <select name="end_year" id="end_year" class="form-select" onchange="validateAndSubmit('end')">
                            @foreach($availableYears as $y) 
                                <option value="{{ $y }}" {{ $endYear == $y ? 'selected' : '' }}>{{ $y }}</option> 
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="trendChart"></canvas>
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

        const dataBulanan = {!! json_encode($dataBulanan) !!};
        const trendLabels = {!! json_encode($trendLabels) !!};
        const trendData = {!! json_encode($trendData) !!};

        // 1. GRAFIK BATANG (BULANAN)
        new Chart(document.getElementById('monthlyChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jml',
                    data: dataBulanan,
                    backgroundColor: '#3498db',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 25 } },
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        display: true, anchor: 'end', align: 'top', color: 'black',
                        font: { weight: 'bold' },
                        // HANYA TAMPILKAN JIKA LEBIH DARI 0
                        formatter: (val) => val > 0 ? val : ''
                    }
                },
                scales: { 
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { borderDash: [2, 2] } } 
                }
            }
        });

        // 2. GRAFIK GARIS (TREN TAHUNAN) - PERBAIKAN VISUAL DISINI
        
        // Logika Visual: Jika data lebih dari 15 tahun, titiknya dikecilkan biar tidak numpuk
        const totalPoints = trendLabels.length;
        const isCrowded = totalPoints > 15; 
        
        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Total',
                    data: trendData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#198754',
                    
                    // -- TITIK DINAMIS --
                    // Kalau data banyak (>15), titik jadi kecil (radius 2). Kalau sedikit, radius 5.
                    pointRadius: isCrowded ? 2 : 5, 
                    pointHoverRadius: isCrowded ? 4 : 7,
                    
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 25, right: 10, left: 10 } },
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        display: true, align: 'top',
                        color: '#198754',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        font: { weight: 'bold' },
                        // -- SEMBUNYIKAN ANGKA 0 --
                        // Ini akan menghilangkan tumpukan angka "00000" di bawah grafik
                        formatter: (val) => val > 0 ? val : '' 
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        display: false,
                        suggestedMax: Math.max(...trendData) + 1 
                    },
                    x: { 
                        grid: { display: false },
                        // -- OFFSET TRUE --
                        // Ini bikin grafik tidak nempel tembok kiri/kanan. 
                        // Jadi kalau cuma 1 tahun, titiknya ada di TENGAH.
                        offset: true, 
                        
                        // Batasi label tahun biar gak dempetan kalau range 1945-2026
                        ticks: { autoSkip: true, maxTicksLimit: 8 } 
                    }
                }
            }
        });
    });

    // --- LOGIKA JS (VALIDASI TAHUN) ---
    function validateAndSubmit(source) {
        const startSelect = document.getElementById('start_year');
        const endSelect = document.getElementById('end_year');
        const form = document.getElementById('formTren');

        let startVal = parseInt(startSelect.value);
        let endVal = parseInt(endSelect.value);

        if (source === 'start') {
            if (startVal > endVal) {
                endSelect.value = startVal;
            }
        } 
        else if (source === 'end') {
            if (endVal < startVal) {
                startSelect.value = endVal;
            }
        }
        form.submit();
    }
</script>

@endsection