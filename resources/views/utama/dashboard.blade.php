@extends('layouts.admin')
@section('title', 'Dashboard Utama')
@section('content')
<style>
    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    
    .kpi-title { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 0; }
    .kpi-icon { 
        width: 32px; height: 32px; 
        border: 1px solid var(--border-color); 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center;
        color: var(--text-main);
    }
    .kpi-value { font-size: 1.8rem; font-weight: 700; color: var(--text-main); margin-top: 10px; margin-bottom: 0; line-height: 1; }
    
    .badge-trend-up { background-color: #dcfce7; color: #16a34a; font-size: 0.7rem; padding: 3px 6px; border-radius: 4px; font-weight: 600; margin-left: 8px;}
    .badge-trend-down { background-color: #fee2e2; color: #dc2626; font-size: 0.7rem; padding: 3px 6px; border-radius: 4px; font-weight: 600; margin-left: 8px;}
    
    .kpi-footer { font-size: 0.75rem; color: #9ca3af; margin-top: 15px; }

    .chart-card { background: #ffffff; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    
    /* Tabel Style */
    .table-modern th { border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-weight: 500; font-size: 0.85rem; padding: 12px 15px; }
    .table-modern td { vertical-align: middle; border-bottom: 1px solid #f3f4f6; color: var(--text-main); font-size: 0.85rem; padding: 12px 15px; }
    .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
    .status-pending { background: #fee2e2; color: #dc2626; }
    .status-completed { background: #dcfce7; color: #16a34a; }
</style>

{{-- Header Title & Date Picker --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0" style="color: var(--text-main);">Ringkasan Layanan</h3>
    
    <form action="{{ route('utama.dashboard') }}" method="GET" class="m-0">
        <div class="d-flex align-items-center bg-white border rounded-3 px-3 py-1 shadow-sm" style="border-color: var(--border-color) !important;">
            <i class="bi bi-calendar3 text-muted me-2" style="font-size: 0.85rem;"></i>
            <select name="year" class="form-select form-select-sm border-0 shadow-none fw-medium text-dark bg-transparent" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>
</div>

{{-- 1. KARTU STATISTIK (KPI CARDS) --}}
<div class="row g-4 mb-4">
    {{-- Kartu Grand Total --}}
    <div class="col-12 col-md-3">
        <div class="kpi-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <p class="kpi-title">Total Interaksi</p>
                <div class="kpi-icon"><i class="bi bi-activity"></i></div>
            </div>
            <div class="d-flex align-items-center mt-2">
                <h2 class="kpi-value">{{ number_format($grandTotal, 0, ',', '.') }}</h2>
            </div>
            <div class="kpi-footer">Akumulasi di Tahun {{ $tahun }}</div>
        </div>
    </div>

    {{-- Kartu Pengaduan --}}
    <div class="col-12 col-md-3">
        <div class="kpi-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <p class="kpi-title">Pengaduan Masuk</p>
                <div class="kpi-icon"><i class="bi bi-chat-text"></i></div>
            </div>
            <div class="d-flex align-items-center mt-2">
                <h2 class="kpi-value">{{ number_format($pengaduanTotal, 0, ',', '.') }}</h2>
                @if($pengaduanPending > 0)
                    <span class="badge-trend-down"><i class="bi bi-hourglass-split"></i> {{ $pengaduanPending }} Pending</span>
                @else
                    <span class="badge-trend-up"><i class="bi bi-check"></i> Tuntas</span>
                @endif
            </div>
            <div class="kpi-footer">Selesai: {{ $pengaduanSelesai }} pengaduan</div>
        </div>
    </div>

    {{-- Kartu Permintaan --}}
    <div class="col-12 col-md-3">
        <div class="kpi-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <p class="kpi-title">Permintaan Info</p>
                <div class="kpi-icon"><i class="bi bi-info-circle"></i></div>
            </div>
            <div class="d-flex align-items-center mt-2">
                <h2 class="kpi-value">{{ number_format($permintaanTotal, 0, ',', '.') }}</h2>
            </div>
            <div class="kpi-footer">WA: {{ $permintaanChat }} | Telfon: {{ $permintaanTelfon }}</div>
        </div>
    </div>

    {{-- Kartu LaSehat --}}
    <div class="col-12 col-md-3">
        <div class="kpi-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <p class="kpi-title">Order LaSehat</p>
                <div class="kpi-icon"><i class="bi bi-car-front"></i></div>
            </div>
            <div class="d-flex align-items-center mt-2">
                <h2 class="kpi-value">{{ number_format($lasehatTotal, 0, ',', '.') }}</h2>
                <span class="badge-trend-up"><i class="bi bi-arrow-up"></i> Layanan</span>
            </div>
            <div class="kpi-footer">Pengantaran Pasien Selesai</div>
        </div>
    </div>
</div>

{{-- 2. GRAFIK PERBANDINGAN --}}
<div class="row g-4 mb-4">
    {{-- Bar Chart --}}
    <div class="col-lg-8">
        <div class="chart-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="font-size: 1.1rem;">Analitik Volume Layanan</h5>
                <span class="text-muted" style="font-size: 0.85rem;">Tahun {{ $tahun }}</span>
            </div>
            <div style="height: 280px; width: 100%;">
                <canvas id="mainBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Doughnut Chart (Meniru Stacked di Gambar Referensi) --}}
    <div class="col-lg-4">
        <div class="chart-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="font-size: 1.1rem;">Proporsi Layanan</h5>
            </div>
            <div class="d-flex justify-content-center align-items-center" style="height: 250px; width: 100%;">
                <canvas id="mainDoughnutChart"></canvas>
            </div>
            <div class="mt-3 d-flex flex-wrap justify-content-center gap-3" style="font-size: 0.8rem;">
                <span class="d-flex align-items-center"><span style="width:10px;height:10px;background:#f25c3a;border-radius:50%;margin-right:6px;"></span> Pengaduan</span>
                <span class="d-flex align-items-center"><span style="width:10px;height:10px;background:#111827;border-radius:50%;margin-right:6px;"></span> Informasi</span>
                <span class="d-flex align-items-center"><span style="width:10px;height:10px;background:#9ca3af;border-radius:50%;margin-right:6px;"></span> LaSehat</span>
            </div>
        </div>
    </div>
</div>

{{-- 3. TABEL RIWAYAT TERBARU (Mengikuti desain bawah referensi) --}}
<div class="chart-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0" style="font-size: 1.1rem;">Riwayat Layanan Terbaru</h5>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 200px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Cari data...">
            </div>
            <button class="btn btn-sm btn-light border d-flex align-items-center gap-2"><i class="bi bi-sort-down"></i> Filter</button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-modern table-borderless">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                    <th>ID Tiket</th>
                    <th>Tanggal</th>
                    <th>Nama Pasien/Pemohon</th>
                    <th>Kategori Layanan</th>
                    <th>Status</th>
                    <th>Unit Tujuan</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dummy Data disesuaikan dengan layout referensi -->
                <tr>
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td class="fw-medium text-dark">#PGD-8789</td>
                    <td>{{ date('d M Y') }}</td>
                    <td>Bapak Oliver John</td>
                    <td>Pengaduan Pelayanan</td>
                    <td><span class="status-pill status-pending">Pending</span></td>
                    <td class="fw-medium">Poli Umum</td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td class="fw-medium text-dark">#INF-8790</td>
                    <td>{{ date('d M Y', strtotime('-1 days')) }}</td>
                    <td>Ibu Siti Aminah</td>
                    <td>Informasi Jadwal Dokter</td>
                    <td><span class="status-pill status-completed">Selesai</span></td>
                    <td class="fw-medium">Humas</td>
                </tr>
                <tr>
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td class="fw-medium text-dark">#LSH-8791</td>
                    <td>{{ date('d M Y', strtotime('-2 days')) }}</td>
                    <td>Bp. Ahmad Subagyo</td>
                    <td>Antar Pasien (LaSehat)</td>
                    <td><span class="status-pill status-completed">Selesai</span></td>
                    <td class="fw-medium">IGD</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labels = {!! json_encode($chartLabels) !!};
        const dataValues = {!! json_encode($chartData) !!};
        
        // Warna Chart disesuaikan dengan tema Finexy di gambar
        const chartColors = ['#f25c3a', '#111827', '#9ca3af']; 

        // BAR CHART (Mirip grafik kiri di gambar referensi)
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total',
                    data: dataValues,
                    backgroundColor: '#f25c3a', // Oranye sesuai referensi
                    borderRadius: 8, // Ujung melengkung
                    borderSkipped: false,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#111827', padding: 10, cornerRadius: 8 }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#f3f4f6' }, 
                        border: { display: false }
                    },
                    x: { 
                        grid: { display: false }, 
                        border: { display: false }
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
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '75%',
                plugins: {
                    legend: { display: false } // Legend dipindah ke custom HTML di bawah
                }
            }
        });
    });
</script>
@endsection