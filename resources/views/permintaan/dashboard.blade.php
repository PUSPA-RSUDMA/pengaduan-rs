@extends('layouts.admin')
@section('title', 'Dashboard Layanan & Informasi')
@section('content')

<style>
    /* Global Dashboard Styles matching the modern reference */
    body {
        background-color: #f4f7f6; /* Soft background */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .card { 
        border-radius: 16px !important; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; 
        border: 1px solid rgba(0,0,0,0.02) !important;
        background-color: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-card:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 8px 25px rgba(0,0,0,0.06) !important; 
        z-index: 10; 
    }

    .card-header {
        background-color: transparent !important;
        border-bottom: none !important;
        padding: 1.5rem 1.5rem 0.5rem 1.5rem !important;
    }

    .card-body { padding: 1.5rem !important; }

    /* Icon Wrappers */
    .icon-box {
        width: 42px; height: 42px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 10px;
    }

    /* Typography */
    .text-title { color: #111827; font-weight: 700; font-size: 1.1rem; }
    .text-subtitle { color: #6b7280; font-weight: 500; font-size: 0.85rem; }
    
    .btn-see-details {
        font-size: 0.7rem; font-weight: 600; color: #6b7280;
        border: 1px solid #e5e7eb; border-radius: 20px;
        padding: 0.25rem 0.75rem; background: #fff;
    }

    /* Custom Form Select (Pill shaped) */
    .form-select-custom {
        border-radius: 20px; border: 1px solid #e5e7eb; font-size: 0.8rem;
        font-weight: 500; color: #374151; padding-top: 0.3rem; padding-bottom: 0.3rem;
        box-shadow: none; cursor: pointer;
    }
    .form-select-custom:focus { border-color: #10b981; box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15); }

    /* Modern Table Styling for Top 5 */
    .table-modern { margin-bottom: 0; }
    .table-modern > :not(caption) > * > * {
        padding: 1rem; border-bottom-color: #f3f4f6; vertical-align: middle; background-color: transparent !important;
    }
    .table-modern thead th {
        text-transform: uppercase; font-size: 0.75rem; font-weight: 600; color: #6b7280;
        border-bottom: 2px solid #f3f4f6 !important; letter-spacing: 0.5px;
    }
    .table-modern tbody tr:hover { background-color: #f8fafc !important; }
</style>

{{-- HEADER & FILTER TAHUN --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="m-0 fw-bold text-dark" style="font-size: 1.3rem;">Dashboard Statistik Layanan</h4>
        <small class="text-muted">Ringkasan informasi dan pengaduan pelanggan</small>
    </div>
    <form action="{{ route('dashboard.permintaan') }}" method="GET" class="m-0">
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

{{-- 1. KARTU STATISTIK (REKAP JUMLAH) --}}
<div class="row g-4 mb-4">
    {{-- Card Via Chat --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Via Chat</span>
                    <button class="btn-see-details">Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-1">
                    <div class="icon-box" style="background-color: #e6f7ec; color: #10b981;">
                        <i class="bi bi-whatsapp fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalChat }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Card Via Telfon --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Via Telfon</span>
                    <button class="btn-see-details">Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-1">
                    <div class="icon-box" style="background-color: #e0f2fe; color: #0ea5e9;">
                        <i class="bi bi-telephone-fill fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalTelfon }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Pengaduan --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Pengaduan</span>
                    <button class="btn-see-details">Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-1">
                    <div class="icon-box" style="background-color: #fff1f2; color: #f43f5e;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalPengaduan }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Informasi --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Informasi</span>
                    <button class="btn-see-details">Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-1">
                    <div class="icon-box" style="background-color: #eef2ff; color: #6366f1;">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $totalInformasi }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK DOUGHNUT (PEMISAHAN DATA) --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Metode Penyampaian <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="height: 280px; width: 100%; position: relative;">
                    <canvas id="metodeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Jenis Permintaan <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="height: 280px; width: 100%; position: relative;">
                    <canvas id="jenisChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. GRAFIK KURVA & BAR (UNIT) BERDAMPINGAN --}}
<div class="row g-4 mb-4">
    {{-- LINE CHART - TREN BULANAN --}}
    <div class="col-lg-7 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Kurva Bulanan (Tahun {{ $selectedYear }}) <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
            </div>
            <div class="card-body">
                <div style="height: 320px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- HORIZONTAL BAR CHART - UNIT TERKAIT --}}
    <div class="col-lg-5 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Distribusi Unit Terkait <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
            </div>
            <div class="card-body">
                <div style="height: 320px; position: relative;">
                    <canvas id="unitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. ANALISIS DETAIL SUB-KATEGORI KELUHAN --}}
<div class="card mb-4 border-0">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 border-bottom pb-3">
        <span class="text-title">Analisis Detail Sub-Kategori <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
        
        <form action="{{ route('dashboard.permintaan') }}" method="GET" class="d-flex gap-2 m-0">
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <select name="cat_month" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                <option value="">Bulan: Semua</option>
                @foreach(['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                    <option value="{{ $num }}" {{ $catMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="cat_year" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                @foreach($availableYears as $y) 
                    <option value="{{ $y }}" {{ $catYear == $y ? 'selected' : '' }}>Tahun: {{ $y }}</option> 
                @endforeach
            </select>

            <select name="cat_sort" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                <option value="desc" {{ $catSort == 'desc' ? 'selected' : '' }}>Sort: Terbanyak</option>
                <option value="asc" {{ $catSort == 'asc' ? 'selected' : '' }}>Sort: Terendah</option>
            </select>
        </form>
    </div>

    <div class="card-body bg-light rounded-bottom" style="background-color: #f8fafc !important;">
        <div class="row g-4">
            @foreach($masterKategori as $kat)
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px !important;">
                    <div class="card-header bg-white pt-3 pb-0">
                        <span class="text-title fs-6">{{ $kat->name }}</span>
                    </div>
                    <div class="card-body pt-2">
                        <div style="height: 220px;">
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
<div class="row mb-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Top 5 Nomor HP (Pengirim Terbanyak)</span>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm" style="font-weight: 600;">Data {{ $selectedYear }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 15%; text-align: center;">Peringkat</th>
                                <th style="width: 50%;">Nomor HP / WA</th>
                                <th style="width: 35%;">Total Layanan Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPhones as $index => $phone)
                                <tr>
                                    <td class="ps-4 text-center">
                                        @if($index == 0)
                                            <span class="badge" style="background-color: #d1fae5; color: #065f46; border-radius: 20px; padding: 0.4rem 0.8rem;">#{{ $index + 1 }}</span>
                                        @elseif($index == 1)
                                            <span class="badge" style="background-color: #dbeafe; color: #1e40af; border-radius: 20px; padding: 0.4rem 0.8rem;">#{{ $index + 1 }}</span>
                                        @elseif($index == 2)
                                            <span class="badge" style="background-color: #fef3c7; color: #92400e; border-radius: 20px; padding: 0.4rem 0.8rem;">#{{ $index + 1 }}</span>
                                        @else
                                            <span class="badge" style="background-color: #f3f4f6; color: #374151; border-radius: 20px; padding: 0.4rem 0.8rem;">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary" style="width: 30px; height: 30px;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            {{ $phone->no_hp }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark fs-6">{{ $phone->total }}</span> <span class="text-muted" style="font-size: 0.8rem;">Layanan</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="bi bi-folder2-open fs-2 mb-2 d-block text-light"></i>
                                        Belum ada data untuk tahun {{ $selectedYear }}
                                    </td>
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

        // Global Chart Defaults matching modern look
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#9ca3af';

        const customColors = ['#10b981', '#eab308', '#f43f5e', '#3b82f6', '#8b5cf6', '#14b8a6', '#f97316'];

        const metodeLabels = {!! json_encode($metodeLabels) !!};
        const metodeValues = {!! json_encode($metodeValues) !!};
        
        const jenisLabels = {!! json_encode($jenisLabels) !!};
        const jenisValues = {!! json_encode($jenisValues) !!};
        
        const dataBulanan = {!! json_encode($dataBulanan) !!};

        const unitLabels = {!! json_encode($unitLabels) !!};
        const unitValues = {!! json_encode($unitValues) !!};

        // 1. DOUGHNUT CHART - METODE PENYAMPAIAN
        new Chart(document.getElementById('metodeChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: metodeLabels,
                datasets: [{
                    data: metodeValues,
                    backgroundColor: ['#10b981', '#3b82f6', '#f43f5e', '#eab308'], 
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%', 
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: {size: 11} } },
                    datalabels: { display: false } // Hidden inside doughnut for cleaner look
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
                    backgroundColor: ['#f43f5e', '#8b5cf6', '#10b981', '#f97316'], 
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%', 
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: {size: 11} } },
                    datalabels: { display: false } // Hidden inside doughnut for cleaner look
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
                    borderColor: '#3b82f6', // Modern Blue
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointRadius: 4, pointHoverRadius: 6,
                    tension: 0.4, // Smooth curve
                    fill: true
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        display: true, align: 'top', color: '#6b7280', font: { weight: '600', size: 10 },
                        formatter: (val) => val > 0 ? val : '' 
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f3f4f6', drawBorder: false }, 
                        suggestedMax: Math.max(...dataBulanan, 1) + 2 
                    },
                    x: { grid: { display: false, drawBorder: false } }
                }
            }
        });

        // 4. BAR CHART HORIZONTAL - UNIT TERKAIT
        new Chart(document.getElementById('unitChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: unitLabels,
                datasets: [{
                    data: unitValues,
                    backgroundColor: customColors,
                    borderRadius: 6,
                    barPercentage: 0.7,
                    borderSkipped: false
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
                        color: '#4b5563',
                        anchor: 'end', align: 'right',
                        font: { weight: '600', size: 11 },
                        formatter: (val) => val > 0 ? val : ''
                    }
                },
                scales: {
                    x: { display: false, suggestedMax: Math.max(...unitValues, 1) + 2 },
                    y: { grid: { display: false, drawBorder: false } }
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
                        borderRadius: 6,
                        barPercentage: 0.6,
                        borderSkipped: false
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
                            color: '#4b5563',
                            anchor: 'end', align: 'right', 
                            font: { weight: '600', size: 10 },
                            formatter: (val) => val > 0 ? val : '' 
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) { return ' Jumlah: ' + context.parsed.x; }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, display: false, suggestedMax: Math.max(...data, 1) + 1 },
                        y: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }

        // Generate Chart Sub-Kategori Otomatis
        @foreach($masterKategori as $index => $kat)
            createSubChart(
                'subChart_{{ $kat->id }}', 
                {!! json_encode($subCategoryCounts[$kat->name] ?? []) !!}, 
                customColors[{{ $index }} % customColors.length]
            );
        @endforeach
    });
</script>
@endsection