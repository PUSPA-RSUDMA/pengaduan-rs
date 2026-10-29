@extends('layouts.admin')
@section('title', 'Dashboard Monitoring')
@section('content')

<style>
    /* Global Dashboard Styles matching the image */
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

    .card-body {
        padding: 1.5rem !important;
    }

    /* Icon Wrappers */
    .icon-box {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    /* Typography & Badges */
    .text-title {
        color: #111827;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .text-subtitle {
        color: #6b7280;
        font-weight: 500;
        font-size: 0.85rem;
    }
    
    .btn-see-details {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 0.25rem 0.75rem;
        background: #fff;
    }

    .badge-percent {
        border-radius: 20px;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    /* Custom Form Select (Pill shaped) */
    .form-select-custom {
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        padding-top: 0.3rem;
        padding-bottom: 0.3rem;
        box-shadow: none;
        cursor: pointer;
    }
    .form-select-custom:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
    }
</style>

{{-- 1. KARTU STATISTIK --}}
<div class="row g-4 mb-4">
    {{-- Card Total Masuk --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Total Masuk <i class="bi bi-info-circle ms-1 text-muted" style="font-size:10px;"></i></span>
                    <button class="btn-see-details">See Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-box" style="background-color: #e6f7ec; color: #10b981;">
                        <i class="bi bi-inbox fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $total }}</h2>
                    <span class="badge-percent bg-success text-white">
                        <i class="bi bi-arrow-up-right"></i> 100%
                    </span>
                </div>
                <div class="text-muted mt-3" style="font-size: 0.75rem;">
                    Seluruh keluhan yang masuk ke sistem
                </div>
            </div>
        </div>
    </div>

    {{-- Card Pending --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Pending <i class="bi bi-info-circle ms-1 text-muted" style="font-size:10px;"></i></span>
                    <button class="btn-see-details">See Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-box" style="background-color: #fff1f2; color: #f43f5e;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $pending }}</h2>
                </div>
                <div class="text-muted mt-3" style="font-size: 0.75rem;">
                    Menunggu untuk diproses petugas
                </div>
            </div>
        </div>
    </div>

    {{-- Card Sedang Proses --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Sedang Proses <i class="bi bi-info-circle ms-1 text-muted" style="font-size:10px;"></i></span>
                    <button class="btn-see-details">See Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-box" style="background-color: #fefce8; color: #eab308;">
                        <i class="bi bi-gear fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $process ?? 0 }}</h2>
                </div>
                <div class="text-muted mt-3" style="font-size: 0.75rem;">
                    Keluhan dalam tahap penanganan
                </div>
            </div>
        </div>
    </div>

    {{-- Card Selesai --}}
    <div class="col-6 col-md-3">
        <div class="card h-100 hover-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-subtitle">Selesai <i class="bi bi-info-circle ms-1 text-muted" style="font-size:10px;"></i></span>
                    <button class="btn-see-details">See Details</button>
                </div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="icon-box" style="background-color: #f0fdf4; color: #22c55e;">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.8rem;">{{ $done }}</h2>
                </div>
                <div class="text-muted mt-3" style="font-size: 0.75rem;">
                    Penanganan keluhan telah tuntas
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK BULANAN & TREN --}}
<div class="row g-4 mb-4">
    {{-- CHART BULANAN (KIRI) --}}
    <div class="col-lg-7 col-12"> 
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Statistik Bulanan <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
                <form action="{{ route('dashboard') }}" method="GET" class="m-0">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <select name="year" class="form-select form-select-custom" onchange="this.form.submit()">
                        @foreach($availableYears as $year) 
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Year: {{ $year }}</option> 
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div style="height: 320px; position: relative;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART TREN (KANAN) --}}
    <div class="col-lg-5 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Tren Tahunan <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
                <form action="{{ route('dashboard') }}" method="GET" id="formTren" class="m-0">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <div class="d-flex gap-2">
                        <select name="start_year" id="start_year" class="form-select form-select-custom w-auto" onchange="validateAndSubmit('start')">
                            @foreach($availableYears as $y) <option value="{{ $y }}" {{ $startYear == $y ? 'selected' : '' }}>{{ $y }}</option> @endforeach
                        </select>
                        <span class="d-flex align-items-center text-muted" style="font-size: 0.8rem;">to</span>
                        <select name="end_year" id="end_year" class="form-select form-select-custom w-auto" onchange="validateAndSubmit('end')">
                            @foreach($availableYears as $y) <option value="{{ $y }}" {{ $endYear == $y ? 'selected' : '' }}>{{ $y }}</option> @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div style="height: 320px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. GRAFIK UNIT & MEDIA --}}
<div class="row g-4 mb-4">
    {{-- CHART UNIT TUJUAN (KIRI) --}}
    <div class="col-lg-7 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Distribusi Unit Tujuan <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
                <form action="{{ route('dashboard') }}" method="GET" class="m-0">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <select name="year" class="form-select form-select-custom" onchange="this.form.submit()">
                        @foreach($availableYears as $year) 
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Year: {{ $year }}</option> 
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="unitChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART MEDIA PENGADUAN (KANAN) --}}
    <div class="col-lg-5 col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-title">Media Pengaduan <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
                <form action="{{ route('dashboard') }}" method="GET" class="m-0">
                    <input type="hidden" name="start_year" value="{{ $startYear }}">
                    <input type="hidden" name="end_year" value="{{ $endYear }}">
                    <input type="hidden" name="cat_month" value="{{ $catMonth }}">
                    <input type="hidden" name="cat_year" value="{{ $catYear }}">
                    <input type="hidden" name="cat_sort" value="{{ $catSort }}">
                    <select name="year" class="form-select form-select-custom" onchange="this.form.submit()">
                        @foreach($availableYears as $year) 
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Year: {{ $year }}</option> 
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="sourceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. FITUR ANALISIS SUB-KATEGORI --}}
<div class="card mb-4 border-0">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 border-bottom pb-3">
        <span class="text-title">Analisis Detail Sub-Kategori <i class="bi bi-info-circle ms-1 text-muted" style="font-size: 14px;"></i></span>
        
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2 m-0">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <input type="hidden" name="start_year" value="{{ $startYear }}">
            <input type="hidden" name="end_year" value="{{ $endYear }}">

            <select name="cat_month" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                <option value="">Month: All</option>
                @foreach(['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'] as $num => $name)
                    <option value="{{ $num }}" {{ $catMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="cat_year" class="form-select form-select-custom w-auto" onchange="this.form.submit()">
                @foreach($availableYears as $y) <option value="{{ $y }}" {{ $catYear == $y ? 'selected' : '' }}>Year: {{ $y }}</option> @endforeach
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.1.0"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.register(ChartDataLabels);

        // Styling defaults untuk menyamai gambar
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#9ca3af'; // soft gray untuk text axis

        // Warna untuk Chart Bar yang lebih modern (menyerupai gambar: hijau, kuning, merah soft)
        const customColors = ['#10b981', '#eab308', '#f43f5e', '#3b82f6', '#8b5cf6', '#14b8a6', '#f97316'];
        
        // 1. Bulanan (Bar)
        new Chart(document.getElementById('monthlyChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{ 
                    data: {!! json_encode($dataBulanan) !!}, 
                    backgroundColor: function(context) {
                        // Membuat bar bergantian warnanya atau menggunakan warna primary
                        return context.dataIndex % 2 === 0 ? '#e2e8f0' : '#10b981'; // Mix abu-abu halus dan hijau
                    },
                    borderRadius: 6, // ujung tumpul
                    borderSkipped: false,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false }, 
                    datalabels: { 
                        anchor: 'end', 
                        align: 'top', 
                        font: { weight: '600', size: 10 }, 
                        color: '#6b7280',
                        formatter: (v) => v > 0 ? v : '' 
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        border: { display: false },
                        grid: { color: '#f3f4f6', drawBorder: false }, // grid transparan
                        ticks: { precision: 0 } 
                    },
                    x: {
                        grid: { display: false, drawBorder: false } // sembunyikan grid vertikal
                    }
                }
            }
        });

        // 2. Tren (Line)
        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{ 
                    data: {!! json_encode($trendData) !!}, 
                    borderColor: '#10b981', 
                    backgroundColor: 'rgba(16, 185, 129, 0.1)', 
                    fill: true, 
                    tension: 0.4, // Membuat line melengkung smooth
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false }, 
                    datalabels: { 
                        align: 'top', 
                        font: { weight: '600', size: 10 }, 
                        color: '#6b7280',
                        formatter: (v) => v > 0 ? v : '' 
                    } 
                },
                scales: { 
                    y: { display: false, beginAtZero: true }, 
                    x: { grid: { display: false, drawBorder: false } } 
                }
            }
        });

        // 3. Unit (Horizontal Bar)
        const uLabels = {!! json_encode($unitLabels) !!};
        const uVals = {!! json_encode($unitValues) !!};
        new Chart(document.getElementById('unitChart').getContext('2d'), {
            type: 'bar',
            data: { 
                labels: uLabels, 
                datasets: [{ 
                    data: uVals, 
                    backgroundColor: customColors, 
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.7
                }] 
            },
            options: { 
                indexAxis: 'y', 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false }, 
                    datalabels: { 
                        anchor: 'end', align: 'right', 
                        font: { weight: '600', size: 11 }, 
                        color: '#4b5563',
                        formatter: (v) => v > 0 ? v : '' 
                    } 
                }, 
                scales: { 
                    x: { display: false }, 
                    y: { grid: { display: false, drawBorder: false } } 
                },
                layout: { padding: { right: 30 } }
            }
        });

        // 4. Source (Doughnut) - dibuat lebih bersih dengan warna soft
        const sLabels = {!! json_encode($sourceLabels) !!};
        const sVals = {!! json_encode($sourceValues) !!};
        new Chart(document.getElementById('sourceChart').getContext('2d'), {
            type: 'doughnut',
            data: { 
                labels: sLabels, 
                datasets: [{ 
                    data: sVals, 
                    backgroundColor: customColors,
                    borderWidth: 0 // hapus border putih agar lebih modern
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '70%', // lubang lebih besar
                plugins: { 
                    legend: { 
                        position: 'right',
                        labels: { usePointStyle: true, padding: 20, font: {size: 11} }
                    },
                    datalabels: { display: false } // sembunyikan label di dalam donat agar lebih rapi
                } 
            }
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
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6
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
                        x: { 
                            beginAtZero: true, display: false, 
                            suggestedMax: Math.max(...data, 1) + 1 
                        },
                        y: { 
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10 } } 
                        }
                    }
                }
            });
        }

        // GENERATE CHART SUB-KATEGORI SECARA OTOMATIS
        @foreach($masterKategori as $index => $kat)
            createSubChart(
                'subChart_{{ $kat->id }}', 
                {!! json_encode($subCategoryCounts[$kat->name] ?? []) !!}, 
                customColors[{{ $index }} % customColors.length]
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