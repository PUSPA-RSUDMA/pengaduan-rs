@extends('layouts.admin')
@section('title', 'Dashboard Layanan & Informasi')
@section('content')
<style>
    .hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: default; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; z-index: 10; }
</style>

{{-- HEADER & FILTER TAHUN --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold text-dark"><i class="bi bi-speedometer2 me-2"></i>Dashboard Statistik</h4>
    <form action="{{ route('dashboard.permintaan') }}" method="GET" class="m-0">
        <div class="input-group input-group-sm shadow-sm">
            <span class="input-group-text bg-primary text-white border-primary"><i class="bi bi-calendar-event me-1"></i> Tahun</span>
            <select name="year" class="form-select fw-bold border-primary" onchange="this.form.submit()">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- 1. KARTU STATISTIK (REKAP JUMLAH) --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card bg-success text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-white-50 small mb-1 text-uppercase">Via Chat</h6><h2 class="fw-bold mb-0">{{ $totalChat }}</h2></div>
                <i class="bi bi-whatsapp fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-info text-dark border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-dark-50 small mb-1 text-uppercase">Via Telfon</h6><h2 class="fw-bold mb-0">{{ $totalTelfon }}</h2></div>
                <i class="bi bi-telephone-fill fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-danger text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-white-50 small mb-1 text-uppercase">Pengaduan</h6><h2 class="fw-bold mb-0">{{ $totalPengaduan }}</h2></div>
                <i class="bi bi-exclamation-triangle-fill fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-primary text-white border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div><h6 class="text-white-50 small mb-1 text-uppercase">Informasi</h6><h2 class="fw-bold mb-0">{{ $totalInformasi }}</h2></div>
                <i class="bi bi-info-circle-fill fs-1 opacity-25"></i>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK DOUGHNUT (PEMISAHAN DATA) --}}
<div class="row mb-4 g-4">
    <div class="col-lg-6 col-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <span class="fw-bold text-dark"><i class="bi bi-headset me-2 text-success"></i>Metode Penyampaian</span>
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="height: 250px; width: 100%;">
                    <canvas id="metodeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <span class="fw-bold text-dark"><i class="bi bi-tags-fill me-2 text-danger"></i>Jenis Permintaan</span>
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="height: 250px; width: 100%;">
                    <canvas id="jenisChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. GRAFIK KURVA & BAR (UNIT) BERDAMPINGAN --}}
<div class="row g-4 mb-4">
    {{-- LINE CHART - TREN BULANAN --}}
    <div class="col-lg-6 col-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <span class="fw-bold text-primary"><i class="bi bi-graph-up-arrow me-2"></i>Kurva Bulanan (Tahun {{ $selectedYear }})</span>
            </div>
            <div class="card-body">
                <div style="height: 350px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- HORIZONTAL BAR CHART - UNIT TERKAIT --}}
    <div class="col-lg-6 col-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <span class="fw-bold text-info"><i class="bi bi-building me-2"></i>Distribusi Unit Terkait</span>
            </div>
            <div class="card-body">
                <div style="height: 350px; position: relative;">
                    <canvas id="unitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. ANALISIS DETAIL SUB-KATEGORI KELUHAN (DINAMIS DARI DATABASE) --}}
<div class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 border-bottom">
        <span class="fw-bold text-primary fs-5"><i class="bi bi-grid-1x2-fill me-2"></i> Analisis Detail Sub-Kategori Keluhan</span>
        
        {{-- Form Filter Sub-Kategori --}}
        <form action="{{ route('dashboard.permintaan') }}" method="GET" class="d-flex gap-2 m-0">
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <!-- Filter Bulan -->
            <select name="cat_month" class="form-select form-select-sm border-secondary shadow-sm" onchange="this.form.submit()">
                <option value="">- Semua Bulan -</option>
                @foreach(['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                    <option value="{{ $num }}" {{ $catMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <!-- Filter Tahun -->
            <select name="cat_year" class="form-select form-select-sm border-secondary shadow-sm" onchange="this.form.submit()">
                @foreach($availableYears as $y) 
                    <option value="{{ $y }}" {{ $catYear == $y ? 'selected' : '' }}>{{ $y }}</option> 
                @endforeach
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

{{-- 5. DAFTAR TOP NOMOR HP TERBANYAK --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="bi bi-telephone-inbound text-warning me-2"></i>Top 5 Nomor HP (Pengirim Terbanyak)
                </span>
                <span class="badge bg-warning text-dark">Tahun {{ $selectedYear }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 10%;">Peringkat</th>
                                <th style="width: 50%;">Nomor HP / WA</th>
                                <th style="width: 40%;">Total Permintaan (Masuk)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPhones as $index => $phone)
                                <tr>
                                    <td class="ps-4">
                                        @if($index == 0)
                                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6">#{{ $index + 1 }}</span>
                                        @elseif($index == 1)
                                            <span class="badge bg-primary rounded-pill px-3 py-2">#{{ $index + 1 }}</span>
                                        @elseif($index == 2)
                                            <span class="badge bg-info rounded-pill px-3 py-2">#{{ $index + 1 }}</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-secondary">{{ $phone->no_hp }}</td>
                                    <td>
                                        <span class="fw-bold fs-6">{{ $phone->total }}</span> Layanan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada data untuk tahun {{ $selectedYear }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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

        const metodeLabels = {!! json_encode($metodeLabels) !!};
        const metodeValues = {!! json_encode($metodeValues) !!};
        
        const jenisLabels = {!! json_encode($jenisLabels) !!};
        const jenisValues = {!! json_encode($jenisValues) !!};
        
        const dataBulanan = {!! json_encode($dataBulanan) !!};

        const unitLabels = {!! json_encode($unitLabels) !!};
        const unitValues = {!! json_encode($unitValues) !!};

        const generateColors = (count) => {
            const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0', '#fd7e14', '#20c997', '#6610f2', '#e83e8c'];
            return Array.from({ length: count }, (_, i) => colors[i % colors.length]);
        };

        // 1. DOUGHNUT CHART - METODE PENYAMPAIAN
        new Chart(document.getElementById('metodeChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: metodeLabels,
                datasets: [{
                    data: metodeValues,
                    backgroundColor: ['#198754', '#0dcaf0'], 
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
                        formatter: (val) => val > 0 ? val : '' 
                    }
                }
            }
        });

        // 2. DOUGHNUT CHART - JENIS PERMINTAAN
        new Chart(document.getElementById('jenisChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: jenisLabels,
                datasets: [{
                    data: jenisValues,
                    backgroundColor: ['#dc3545', '#0d6efd'], 
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
                        formatter: (val) => val > 0 ? val : '' 
                    }
                }
            }
        });

        // 3. LINE CHART - KURVA TREN BULANAN
        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Total Masuk',
                    data: dataBulanan,
                    borderColor: '#6f42c1', 
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6f42c1',
                    pointRadius: 5, pointHoverRadius: 8,
                    tension: 0.4, 
                    fill: true
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                layout: { padding: { top: 20 } },
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        display: true, align: 'top', color: '#6f42c1', font: { weight: 'bold' },
                        formatter: (val) => val > 0 ? val : '' 
                    }
                },
                scales: {
                    y: { beginAtZero: true, display: true, grid: { borderDash: [3, 3] }, suggestedMax: Math.max(...dataBulanan) + 2 },
                    x: { grid: { display: false } }
                }
            }
        });

        // 4. BAR CHART HORIZONTAL - UNIT TERKAIT
        new Chart(document.getElementById('unitChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: unitLabels,
                datasets: [{
                    label: 'Total Layanan',
                    data: unitValues,
                    backgroundColor: generateColors(unitLabels.length),
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: 'black',
                        anchor: 'end',
                        align: 'right',
                        font: { weight: 'bold' },
                        formatter: (val) => val > 0 ? val : ''
                    }
                },
                scales: {
                    x: { beginAtZero: true, suggestedMax: Math.max(...unitValues) + 2, display: false },
                    y: { grid: { display: false } }
                }
            }
        });

        // 5. CHART SUB-KATEGORI DINAMIS
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
                    indexAxis: 'y',
                    responsive: true, 
                    maintainAspectRatio: false,
                    layout: { padding: { right: 30 } },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: 'black',
                            anchor: 'end', 
                            align: 'right', 
                            font: { weight: 'bold', size: 12 },
                            formatter: (val) => val > 0 ? val : '' 
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

        const subColors = ['#0d6efd', '#dc3545', '#fd7e14', '#198754', '#ffc107', '#6c757d', '#6f42c1', '#0dcaf0', '#20c997', '#e83e8c'];

        @foreach($masterKategori as $index => $kat)
            createSubChart(
                'subChart_{{ $kat->id }}', 
                {!! json_encode($subCategoryCounts[$kat->name] ?? []) !!}, 
                subColors[{{ $index }} % subColors.length]
            );
        @endforeach
    });
</script>
@endsection