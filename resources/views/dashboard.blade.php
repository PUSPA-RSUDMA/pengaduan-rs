@extends('layouts.admin')
@section('title', 'Dashboard Monitoring')
@section('content')
<style>
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

{{-- 2. GRAFIK BULANAN & TREN --}}
<div class="row">
    {{-- CHART BULANAN (KIRI) --}}
    <div class="col-lg-6 col-12 mb-4"> 
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i> Statistik Bulanan</span>
                <form action="{{ route('dashboard') }}" method="GET">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <select name="year" class="form-select form-select-sm fw-bold border-primary" onchange="this.form.submit()">
                        @foreach($availableYears as $year) <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option> @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body"><div style="height: 300px; position: relative;"><canvas id="monthlyChart"></canvas></div></div>
        </div>
    </div>

    {{-- CHART TREN (KANAN) --}}
    <div class="col-lg-6 col-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 border-bottom-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-success"><i class="bi bi-graph-up-arrow me-2"></i> Tren Tahunan</span>
                </div>
                <form action="{{ route('dashboard') }}" method="GET" id="formTren">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <div class="input-group input-group-sm">
                        <select name="start_year" id="start_year" class="form-select" onchange="validateAndSubmit('start')">
                            @foreach($availableYears as $y) <option value="{{ $y }}" {{ $startYear == $y ? 'selected' : '' }}>{{ $y }}</option> @endforeach
                        </select>
                        <span class="input-group-text bg-light text-muted">s/d</span>
                        <select name="end_year" id="end_year" class="form-select" onchange="validateAndSubmit('end')">
                            @foreach($availableYears as $y) <option value="{{ $y }}" {{ $endYear == $y ? 'selected' : '' }}>{{ $y }}</option> @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="card-body"><div style="height: 300px; position: relative;"><canvas id="trendChart"></canvas></div></div>
        </div>
    </div>
</div>

{{-- 3. GRAFIK UNIT & MEDIA --}}
<div class="row">
    {{-- CHART UNIT TUJUAN (KIRI) --}}
    <div class="col-lg-6 col-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-info"><i class="bi bi-building me-2"></i> Distribusi Unit Tujuan</span>
                <form action="{{ route('dashboard') }}" method="GET" class="m-0">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <select name="year" class="form-select form-select-sm fw-bold border-info" onchange="this.form.submit()">
                        @foreach($availableYears as $year) <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option> @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body"><div style="height: 300px; position: relative;"><canvas id="unitChart"></canvas></div></div>
        </div>
    </div>

    {{-- CHART MEDIA PENGADUAN (KANAN) --}}
    <div class="col-lg-6 col-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-warning"><i class="bi bi-megaphone me-2"></i> Media Pengaduan</span>
                <form action="{{ route('dashboard') }}" method="GET" class="m-0">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <select name="year" class="form-select form-select-sm fw-bold border-warning" onchange="this.form.submit()">
                        @foreach($availableYears as $year) <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option> @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body"><div style="height: 300px; position: relative;"><canvas id="sourceChart"></canvas></div></div>
        </div>
    </div>
</div>

{{-- 4. FITUR ANALISIS SUB-KATEGORI (100% DINAMIS BERDASARKAN DATABASE) --}}
<div class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 border-bottom">
        <span class="fw-bold text-primary fs-5"><i class="bi bi-grid-1x2-fill me-2"></i> Analisis Detail Sub-Kategori Keluhan</span>
        
        {{-- Form Filter Utama untuk Grafik Sub-Kategori --}}
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2 m-0">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <input type="hidden" name="start_year" value="{{ $startYear }}">
            <input type="hidden" name="end_year" value="{{ $endYear }}">

            <!-- Filter Bulan -->
            <select name="cat_month" class="form-select form-select-sm border-secondary shadow-sm" onchange="this.form.submit()">
                <option value="">- Semua Bulan -</option>
                @foreach(['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                    <option value="{{ $num }}" {{ $catMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <!-- Filter Tahun -->
            <select name="cat_year" class="form-select form-select-sm border-secondary shadow-sm" onchange="this.form.submit()">
                @foreach($availableYears as $y) <option value="{{ $y }}" {{ $catYear == $y ? 'selected' : '' }}>{{ $y }}</option> @endforeach
            </select>

            <!-- Filter Sorting -->
            <select name="cat_sort" class="form-select form-select-sm fw-bold border-secondary shadow-sm" onchange="this.form.submit()">
                <option value="desc" {{ $catSort == 'desc' ? 'selected' : '' }}>⬇️ Terbanyak</option>
                <option value="asc" {{ $catSort == 'asc' ? 'selected' : '' }}>⬆️ Terendah</option>
            </select>
        </form>
    </div>

    <div class="card-body">
        <div class="row g-4">
            {{-- LOOPING DINAMIS SEMUA KATEGORI DARI DATABASE --}}
            @foreach($masterKategori as $kat)
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold text-primary py-2">
                        <i class="bi bi-ui-checks-grid me-2"></i> {{ $kat->name }}
                    </div>
                    <div class="card-body">
                        <div style="height: 200px;">
                            <canvas id="subChart_{{ $kat->id }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.1.0"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.register(ChartDataLabels);

        // --- CHART UTAMA ---
        const generateColors = (count) => {
            const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0', '#fd7e14', '#20c997', '#6610f2', '#e83e8c'];
            return Array.from({ length: count }, (_, i) => colors[i % colors.length]);
        };

        // 1. Bulanan
        new Chart(document.getElementById('monthlyChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{ data: {!! json_encode($dataBulanan) !!}, backgroundColor: '#3498db', borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'top', font: { weight: 'bold' }, formatter: (v) => v > 0 ? v : '' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // 2. Tren
        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{ data: {!! json_encode($trendData) !!}, borderColor: '#198754', backgroundColor: 'rgba(25, 135, 84, 0.1)', fill: true, tension: 0.3 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, datalabels: { align: 'top', font: { weight: 'bold' }, formatter: (v) => v > 0 ? v : '' } },
                scales: { y: { display: false, beginAtZero: true }, x: { grid: { display: false }, offset: true } }
            }
        });

        // 3. Unit & Media (Horizontal Bar & Doughnut)
        const uLabels = {!! json_encode($unitLabels) !!}, uVals = {!! json_encode($unitValues) !!};
        new Chart(document.getElementById('unitChart').getContext('2d'), {
            type: 'bar',
            data: { labels: uLabels, datasets: [{ data: uVals, backgroundColor: generateColors(uLabels.length), borderRadius: 4 }] },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'right', font: { weight: 'bold' }, formatter: (v) => v > 0 ? v : '' } }, scales: { x: { display: false }, y: { grid: { display: false } } } }
        });

        const sLabels = {!! json_encode($sourceLabels) !!}, sVals = {!! json_encode($sourceValues) !!};
        new Chart(document.getElementById('sourceChart').getContext('2d'), {
            type: 'doughnut',
            data: { labels: sLabels, datasets: [{ data: sVals, backgroundColor: generateColors(sLabels.length) }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '60%', plugins: { datalabels: { color: '#fff', font: { weight: 'bold' }, formatter: (v) => v > 0 ? v : '' } } }
        });


        // =========================================================
        // --- FUNGSI REUSABLE SUB-KATEGORI DINAMIS ---
        // =========================================================
        function createSubChart(ctxId, dataObj, colorCode) {
            const canvasEl = document.getElementById(ctxId);
            if (!canvasEl) return;

            let labels = Object.keys(dataObj);
            let data = Object.values(dataObj);

            new Chart(canvasEl.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colorCode,
                        borderRadius: 4,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    indexAxis: 'y', // BAR HORIZONTAL AGAR TEKS PANJANG TERBACA
                    responsive: true, 
                    maintainAspectRatio: false,
                    layout: { padding: { right: 30 } }, // Ruang untuk angka di ujung kanan
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: 'black',
                            anchor: 'end', 
                            align: 'right', 
                            font: { weight: 'bold', size: 12 },
                            formatter: (val) => val > 0 ? val : '' // Sembunyikan angka 0
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' Jumlah: ' + context.parsed.x;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            beginAtZero: true, 
                            display: false, 
                            suggestedMax: Math.max(...data, 1) + 1 
                        },
                        y: { 
                            grid: { display: false },
                            ticks: { font: { size: 11 } } 
                        }
                    }
                }
            });
        }

        // Palette warna otomatis untuk grafik sub-kategori
        const subColors = ['#0d6efd', '#dc3545', '#fd7e14', '#198754', '#ffc107', '#6c757d', '#6f42c1', '#0dcaf0', '#20c997', '#e83e8c'];

        // GENERATE CHART SUB-KATEGORI SECARA OTOMATIS BERDASARKAN DATA DARI CONTROLLER
        @foreach($masterKategori as $index => $kat)
            createSubChart(
                'subChart_{{ $kat->id }}', 
                {!! json_encode($subCategoryCounts[$kat->name] ?? []) !!}, 
                subColors[{{ $index }} % subColors.length]
            );
        @endforeach
    });

    function validateAndSubmit(source) {
        const startSelect = document.getElementById('start_year');
        const endSelect = document.getElementById('end_year');
        const form = document.getElementById('formTren');

        if (source === 'start' && parseInt(startSelect.value) > parseInt(endSelect.value)) endSelect.value = startSelect.value;
        else if (source === 'end' && parseInt(endSelect.value) < parseInt(startSelect.value)) startSelect.value = endSelect.value;
        form.submit();
    }
</script>
@endsection