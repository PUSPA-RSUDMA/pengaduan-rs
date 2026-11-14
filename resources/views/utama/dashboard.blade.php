@extends('layouts.admin')
@section('title', 'Ringkasan Eksekutif')
@section('content')
<style>
    .stat-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #f1f5f9;
        padding: 20px 22px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
    }

    .stat-title { font-size: 0.84rem; color: #334155; font-weight: 600; display: flex; align-items: center; gap: 6px; }
    .stat-number { font-size: 2rem; font-weight: 700; color: #0f172a; line-height: 1.1; }
    .stat-desc { font-size: 0.75rem; color: #64748b; margin-top: 12px; margin-bottom: 0; }

    .btn-details {
        font-size: 0.7rem;
        padding: 4px 11px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        color: #475569;
        text-decoration: none;
        font-weight: 600;
        background: #fff;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-details:hover { background: #0fa968; color: #ffffff; border-color: #0fa968; }

    .badge-pill-custom {
        font-size: 0.72rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-green  { background: #dcfce7; color: #15803d; }
    .badge-blue   { background: #dbeafe; color: #1d4ed8; }
    .badge-yellow { background: #fef9c3; color: #a16207; }
    .badge-purple { background: #f3e8ff; color: #7e22ce; }
    .badge-cyan   { background: #cffafe; color: #0e7490; }
    .badge-red    { background: #fee2e2; color: #b91c1c; }
    .badge-indigo { background: #e0e7ff; color: #4338ca; }

    .icon-box-small {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .icon-box-small.green  { background: #dcfce7; color: #16a34a; }
    .icon-box-small.blue   { background: #dbeafe; color: #2563eb; }
    .icon-box-small.yellow { background: #fef9c3; color: #ca8a04; }
    .icon-box-small.purple { background: #f3e8ff; color: #9333ea; }
    .icon-box-small.cyan   { background: #cffafe; color: #0891b2; }
    .icon-box-small.red    { background: #fee2e2; color: #dc2626; }
    .icon-box-small.indigo { background: #e0e7ff; color: #4f46e5; }

    .chart-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .chart-card-header { background: transparent; padding: 20px 24px 10px; border-bottom: none; }
    .chart-card-title { font-weight: 700; color: #0f172a; font-size: 1rem; display: flex; align-items: center; gap: 8px; }

    .mini-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        padding: 3px 9px;
        border-radius: 8px;
        font-weight: 600;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
    }

    .rank-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 12px;
        border-radius: 10px;
        background: #f8fafc;
        margin-bottom: 8px;
        font-size: 0.82rem;
    }
    .rank-item:last-child { margin-bottom: 0; }

    .nav-pills-executive .nav-link {
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        padding: 8px 16px;
    }
    .nav-pills-executive .nav-link.active {
        background-color: #0fa968;
        color: #ffffff;
    }

    .table-exec th {
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-exec td {
        font-size: 0.83rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

@php
    $namaBulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $labelPeriode = (!empty($bulan) && isset($namaBulanList[(int)$bulan]))
        ? $namaBulanList[(int)$bulan] . ' ' . $tahun
        : 'Tahun ' . $tahun;
@endphp

{{-- HEADER & FILTER PERIODE --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 mt-1">
    <div>
        <h4 class="fw-bold m-0 text-dark">Dashboard Ringkasan Eksekutif</h4>
        <p class="text-muted small mb-0 mt-1">
            Ikhtisar menyeluruh seluruh layanan publik, pengaduan, informasi, UHC, PKRS, ambulance LaSehat, dan logbook RSUD ({{ $labelPeriode }}).
        </p>
    </div>

    <form action="{{ route('utama.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
        <select name="month" class="form-select border-0 shadow-sm fw-semibold" style="border-radius: 20px; padding-left: 16px; padding-right: 32px; color: #334155; cursor: pointer; font-size: 0.85rem;" onchange="this.form.submit()">
            <option value="">Semua Bulan</option>
            @foreach($namaBulanList as $num => $namaBln)
                <option value="{{ $num }}" {{ (string)$bulan === (string)$num ? 'selected' : '' }}>{{ $namaBln }}</option>
            @endforeach
        </select>

        <select name="year" class="form-select border-0 shadow-sm fw-bold" style="border-radius: 20px; padding-left: 18px; padding-right: 35px; color: #334155; cursor: pointer; font-size: 0.85rem;" onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>

        @if(!empty($bulan))
            <a href="{{ route('utama.dashboard', ['year' => $tahun]) }}" class="btn btn-light border shadow-sm btn-sm px-3" style="border-radius: 20px; font-weight: 600;">Reset</a>
        @endif
    </form>
</div>

{{-- BARIS 1: KARTU UTAMA (TOTAL INTERAKSI, PENGADUAN, PERMINTAAN INFO, PERMOHONAN DOKUMEN) --}}
<div class="row g-3 mb-3">
    {{-- 1. Total Seluruh Aktivitas --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100" style="background: linear-gradient(135deg, #0fa968 0%, #059669 100%); color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title text-white"><i class="bi bi-layers-fill"></i> Total Seluruh Layanan</span>
                <span class="badge bg-white text-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">{{ $labelPeriode }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small" style="background: rgba(255,255,255,0.2); color: #fff;"><i class="bi bi-bar-chart-line-fill"></i></div>
                <h3 class="stat-number text-white mb-0">{{ number_format($grandTotal) }}</h3>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 pt-1">
                <span class="badge rounded-pill" style="background: rgba(255,255,255,0.18); font-weight: 500; font-size: 0.72rem;">
                    <i class="bi bi-person-badge me-1"></i>{{ $totalUsers }} Akun Petugas
                </span>
                <span class="badge rounded-pill" style="background: rgba(255,255,255,0.18); font-weight: 500; font-size: 0.72rem;">
                    <i class="bi bi-person-vcard me-1"></i>{{ $totalSupir }} Supir LaSehat
                </span>
            </div>
        </div>
    </div>

    {{-- 2. Pengaduan Masyarakat --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Pengaduan Masyarakat</span>
                <a href="{{ route('dashboard') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small green"><i class="bi bi-chat-square-text-fill"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($pengaduanTotal) }}</h3>
                @php
                    $persenPengaduanSelesai = $pengaduanTotal > 0 ? round(($pengaduanSelesai / $pengaduanTotal) * 100) : 0;
                @endphp
                <span class="badge-pill-custom badge-green"><i class="bi bi-check2-circle"></i> {{ $persenPengaduanSelesai }}% Selesai</span>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-3">
                <span class="mini-chip text-success"><i class="bi bi-check-circle-fill"></i> {{ $pengaduanSelesai }} Selesai</span>
                <span class="mini-chip text-primary"><i class="bi bi-arrow-repeat"></i> {{ $pengaduanProses }} Proses</span>
                <span class="mini-chip text-warning"><i class="bi bi-hourglass-split"></i> {{ $pengaduanPending }} Pending</span>
                @if($pengaduanKritis > 0)
                    <span class="mini-chip text-danger"><i class="bi bi-exclamation-triangle-fill"></i> {{ $pengaduanKritis }} Merah</span>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. Permintaan Layanan & Info --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Permintaan Layanan & Info</span>
                <a href="{{ route('dashboard.permintaan') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small yellow"><i class="bi bi-headset"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($permintaanTotal) }}</h3>
                <span class="badge-pill-custom badge-yellow"><i class="bi bi-telephone-inbound"></i> Respon Cepat</span>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-3">
                <span class="mini-chip"><i class="bi bi-whatsapp text-success"></i> {{ $permintaanChat }} Chat</span>
                <span class="mini-chip"><i class="bi bi-telephone-fill text-primary"></i> {{ $permintaanTelfon }} Telepon</span>
                <span class="mini-chip"><i class="bi bi-info-circle text-info"></i> {{ $permintaanJenisInfo }} Info</span>
                <span class="mini-chip"><i class="bi bi-exclamation-circle text-danger"></i> {{ $permintaanJenisAdu }} Aduan</span>
            </div>
        </div>
    </div>

    {{-- 4. Permohonan Informasi (Dokumen/PPID) --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Permohonan Informasi</span>
                <a href="{{ route('permohonan-informasi.index') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small blue"><i class="bi bi-file-earmark-text-fill"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($permohonanInfoTotal) }}</h3>
                <span class="badge-pill-custom badge-blue"><i class="bi bi-cloud-check"></i> Google Drive</span>
            </div>
            <p class="stat-desc">
                @if($permohonanInfoTopKeperluan->isNotEmpty())
                    Terbanyak: <strong>{{ $permohonanInfoTopKeperluan->first()->keperluan }}</strong> ({{ $permohonanInfoTopKeperluan->first()->total }} berkas)
                @else
                    Permohonan dokumen & informasi medis pasien.
                @endif
            </p>
        </div>
    </div>
</div>

{{-- BARIS 2: KARTU MODUL LAINNYA (PERALIHAN UHC, KEGIATAN PKRS, MOBIL LASEHAT, AGENDA LOGBOOK) --}}
<div class="row g-3 mb-4">
    {{-- 5. Peralihan UHC --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Peralihan UHC BPJS</span>
                <a href="{{ route('permohonan-uhc.index') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small purple"><i class="bi bi-shield-plus"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($uhcTotal) }}</h3>
                <span class="badge-pill-custom badge-purple"><i class="bi bi-card-checklist"></i> JKN-UHC</span>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-3">
                @forelse($uhcSegmenStats->take(3) as $seg)
                    <span class="mini-chip">{{ $seg->segmen_kepesertaan }}: <strong>{{ $seg->total }}</strong></span>
                @empty
                    <span class="text-muted small">Belum ada pengajuan peralihan UHC.</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 6. Kegiatan PKRS --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Kegiatan Edukasi PKRS</span>
                <a href="{{ route('pkrs.dashboard') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small cyan"><i class="bi bi-megaphone-fill"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($pkrsTotal) }}</h3>
                @php
                    $persenPkrs = $pkrsTotal > 0 ? round(($pkrsSelesai / $pkrsTotal) * 100) : 0;
                @endphp
                <span class="badge-pill-custom badge-cyan"><i class="bi bi-check2-all"></i> {{ $persenPkrs }}% Selesai</span>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-3">
                <span class="mini-chip text-success"><i class="bi bi-check-circle-fill"></i> {{ $pkrsSelesai }} Selesai</span>
                <span class="mini-chip text-primary"><i class="bi bi-hourglass-top"></i> {{ $pkrsProses }} Proses</span>
                <span class="mini-chip text-secondary"><i class="bi bi-clock"></i> {{ $pkrsBelum }} Belum</span>
            </div>
        </div>
    </div>

    {{-- 7. Mobil LaSehat (Ambulance) --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Order Mobil LaSehat</span>
                <a href="{{ route('lasehat.dashboard') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small red"><i class="bi bi-truck-front-fill"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($lasehatTotal) }}</h3>
                <span class="badge-pill-custom badge-red"><i class="bi bi-geo-alt-fill"></i> Antar Pasien</span>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-3">
                <span class="mini-chip text-success"><i class="bi bi-person-check-fill"></i> {{ $lasehatSudahSupir }} Ada Supir</span>
                <span class="mini-chip text-danger"><i class="bi bi-person-dash-fill"></i> {{ $lasehatBelumSupir }} Belum Ditugaskan</span>
            </div>
        </div>
    </div>

    {{-- 8. Agenda Logbook & Pengingat --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="stat-title">Agenda Logbook Acara</span>
                <a href="{{ route('logbook.index') }}" class="btn-details">Detail <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box-small indigo"><i class="bi bi-calendar-event-fill"></i></div>
                <h3 class="stat-number mb-0">{{ number_format($logbookTotal) }}</h3>
                <span class="badge-pill-custom badge-indigo"><i class="bi bi-bell-fill"></i> {{ $logbookUpcoming }} Akan Datang</span>
            </div>
            <p class="stat-desc">
                @if($upcomingAgendas->isNotEmpty())
                    Terdekat: <strong>{{ \Illuminate\Support\Str::limit($upcomingAgendas->first()->judul_acara, 26) }}</strong>
                    ({{ \Carbon\Carbon::parse($upcomingAgendas->first()->tanggal_acara)->format('d/m/Y') }})
                @else
                    Belum ada agenda acara mendatang yang terjadwal.
                @endif
            </p>
        </div>
    </div>
</div>

{{-- BARIS 3: GRAFIK TREN BULANAN (JAN-DES) & PROPORSI KESELURUHAN --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="chart-card-title">
                    <i class="bi bi-graph-up-arrow text-success"></i>
                    Tren Aktivitas Bulanan Seluruh Layanan ({{ $tahun }})
                </span>
            </div>
            <div class="card-body p-4 pt-2">
                <div style="height: 330px; width: 100%;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <span class="chart-card-title">
                    <i class="bi bi-pie-chart-fill text-primary"></i>
                    Proporsi Volume Layanan ({{ $labelPeriode }})
                </span>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center p-4 pt-2">
                <div style="height: 330px; width: 100%;">
                    <canvas id="mainDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BARIS 4: PERBANDINGAN VOLUME ANTAR MODUL & ANALISIS TOP UNIT/RUANGAN/SUPIR --}}
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <span class="chart-card-title">
                    <i class="bi bi-bar-chart-steps text-warning"></i>
                    Perbandingan Volume per Modul ({{ $labelPeriode }})
                </span>
            </div>
            <div class="card-body p-4 pt-2">
                <div style="height: 300px; width: 100%;">
                    <canvas id="mainBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <span class="chart-card-title">
                    <i class="bi bi-trophy-fill text-danger"></i>
                    Peringkat Teratas Unit Tujuan, Ruangan & Operasional ({{ $labelPeriode }})
                </span>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="row g-3">
                    {{-- Top Unit Pengaduan --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold small text-dark mb-3"><i class="bi bi-chat-left-quote-fill text-success me-1"></i> Top 5 Unit Tujuan Pengaduan</h6>
                            @forelse($topUnitPengaduan as $idx => $u)
                                <div class="rank-item">
                                    <span class="text-truncate me-2"><strong>{{ $idx + 1 }}.</strong> {{ $u->unit_destination }}</span>
                                    <span class="badge bg-success rounded-pill">{{ $u->total }}</span>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada data pengaduan.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Top Unit Permintaan Info --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold small text-dark mb-3"><i class="bi bi-headset text-warning me-1"></i> Top 5 Unit Terkait Permintaan Info</h6>
                            @forelse($topUnitPermintaan as $idx => $u)
                                <div class="rank-item">
                                    <span class="text-truncate me-2"><strong>{{ $idx + 1 }}.</strong> {{ $u->unit_terkait }}</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $u->total }}</span>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada data permintaan.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Top Ruangan LaSehat --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold small text-dark mb-3"><i class="bi bi-hospital-fill text-danger me-1"></i> Top 5 Ruangan Order LaSehat</h6>
                            @forelse($topRuanganLasehat as $idx => $r)
                                <div class="rank-item">
                                    <span class="text-truncate me-2"><strong>{{ $idx + 1 }}.</strong> {{ $r->tempat_dirawat }}</span>
                                    <span class="badge bg-danger rounded-pill">{{ $r->total }}</span>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada data ruangan LaSehat.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Top Supir LaSehat --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border bg-light bg-opacity-50 h-100">
                            <h6 class="fw-bold small text-dark mb-3"><i class="bi bi-person-Vcard-fill text-primary me-1"></i> Top 5 Supir Ambulance LaSehat</h6>
                            @forelse($topSupirLasehat as $idx => $s)
                                <div class="rank-item">
                                    <span class="text-truncate me-2"><strong>{{ $idx + 1 }}.</strong> {{ $s->supir_ambulance }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $s->total }} trip</span>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada penugasan supir.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BARIS 5: TABEL RINGKASAN DATA TERBARU LINTAS MODUL & AGENDA MENDATANG --}}
<div class="row g-4 mb-2">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="chart-card-title">
                    <i class="bi bi-clock-history text-success"></i>
                    Aktivitas Terbaru Lintas Layanan
                </span>

                <ul class="nav nav-pills nav-pills-executive gap-1" id="execRecentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-pengaduan" type="button" role="tab">Pengaduan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-permintaan" type="button" role="tab">Permintaan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-dokumen-uhc" type="button" role="tab">Informasi & UHC</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-pkrs" type="button" role="tab">PKRS</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-lasehat" type="button" role="tab">LaSehat</button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4 pt-2">
                <div class="tab-content">
                    {{-- Tab 1: Pengaduan Terbaru --}}
                    <div class="tab-pane fade show active" id="tab-pengaduan" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-exec mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pelapor</th>
                                        <th>Unit Tujuan</th>
                                        <th>Grading</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentComplaints as $item)
                                        <tr>
                                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                            <td class="fw-semibold">{{ $item->reporter_name ?? '-' }}</td>
                                            <td>{{ $item->unit_destination ?? '-' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->grade ?? '-' }}</span></td>
                                            <td>
                                                @if($item->status === 'Selesai')
                                                    <span class="badge-pill-custom badge-green">Selesai</span>
                                                @elseif($item->status === 'Proses')
                                                    <span class="badge-pill-custom badge-blue">Proses</span>
                                                @else
                                                    <span class="badge-pill-custom badge-yellow">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data pengaduan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 2: Permintaan Terbaru --}}
                    <div class="tab-pane fade" id="tab-permintaan" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-exec mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. HP</th>
                                        <th>Metode</th>
                                        <th>Jenis</th>
                                        <th>Unit Terkait</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPermintaan as $item)
                                        <tr>
                                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y') }}</td>
                                            <td class="fw-semibold">{{ $item->no_hp ?? '-' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->metode_penyampaian }}</span></td>
                                            <td>{{ $item->jenis_permintaan }}</td>
                                            <td>{{ $item->unit_terkait ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data permintaan layanan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 3: Permohonan Informasi & Peralihan UHC Terbaru --}}
                    <div class="tab-pane fade" id="tab-dokumen-uhc" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-primary mb-2"><i class="bi bi-file-earmark-text me-1"></i> Permohonan Informasi Terbaru</h6>
                                <div class="table-responsive">
                                    <table class="table table-exec mb-0">
                                        <thead>
                                            <tr>
                                                <th>Pemohon / Pasien</th>
                                                <th>Keperluan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentInfo as $item)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $item->nama_pemohon }}</div>
                                                        <small class="text-muted">Pasien: {{ $item->nama_pasien }}</small>
                                                    </td>
                                                    <td><span class="badge-pill-custom badge-blue">{{ $item->keperluan }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-purple mb-2" style="color:#7e22ce;"><i class="bi bi-shield-plus me-1"></i> Peralihan UHC Terbaru</h6>
                                <div class="table-responsive">
                                    <table class="table table-exec mb-0">
                                        <thead>
                                            <tr>
                                                <th>Pemohon / Pasien</th>
                                                <th>Segmen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentUhc as $item)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $item->nama_pemohon }}</div>
                                                        <small class="text-muted">Pasien: {{ $item->nama_pasien }}</small>
                                                    </td>
                                                    <td><span class="badge-pill-custom badge-purple">{{ $item->segmen_kepesertaan }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 4: Kegiatan PKRS Terbaru --}}
                    <div class="tab-pane fade" id="tab-pkrs" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-exec mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tema Kegiatan</th>
                                        <th>Request By</th>
                                        <th>Output</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPkrs as $item)
                                        <tr>
                                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                            <td class="fw-semibold">{{ \Illuminate\Support\Str::limit($item->tema, 40) }}</td>
                                            <td>{{ $item->request_by }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->output }}</span></td>
                                            <td>
                                                @if($item->status === 'Selesai')
                                                    <span class="badge-pill-custom badge-green">Selesai</span>
                                                @elseif($item->status === 'Proses')
                                                    <span class="badge-pill-custom badge-blue">Proses</span>
                                                @else
                                                    <span class="badge-pill-custom badge-yellow">Belum</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data kegiatan PKRS.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 5: Order Mobil LaSehat Terbaru --}}
                    <div class="tab-pane fade" id="tab-lasehat" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-exec mb-0">
                                <thead>
                                    <tr>
                                        <th>Tgl Antar</th>
                                        <th>Nama Pasien</th>
                                        <th>Ruangan</th>
                                        <th>Tujuan</th>
                                        <th>Supir Ambulance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLasehat as $item)
                                        <tr>
                                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pengantaran)->format('d/m/Y') }}</td>
                                            <td class="fw-semibold">{{ $item->nama_pasien }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->tempat_dirawat }}</span></td>
                                            <td>{{ \Illuminate\Support\Str::limit($item->alamat_tujuan, 30) }}</td>
                                            <td>
                                                @if(!empty($item->supir_ambulance))
                                                    <span class="badge-pill-custom badge-green">{{ $item->supir_ambulance }}</span>
                                                @else
                                                    <span class="badge-pill-custom badge-red">Belum Ada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data order LaSehat.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Agenda Logbook Mendatang --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-card-header d-flex justify-content-between align-items-center">
                <span class="chart-card-title">
                    <i class="bi bi-calendar-check-fill text-indigo" style="color:#4f46e5;"></i>
                    Agenda Logbook Mendatang
                </span>
                <a href="{{ route('logbook.index') }}" class="btn-details">Buka Kalender</a>
            </div>
            <div class="card-body p-4 pt-2">
                @forelse($upcomingAgendas as $agenda)
                    @php
                        $tglAcara = \Carbon\Carbon::parse($agenda->tanggal_acara);
                        $isTomorrow = $tglAcara->isTomorrow();
                        $isToday = $tglAcara->isToday();
                    @endphp
                    <div class="p-3 rounded-4 border mb-2 {{ ($isToday || $isTomorrow) ? 'border-danger bg-danger bg-opacity-10' : 'bg-light bg-opacity-50' }}">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $agenda->judul_acara }}</div>
                            @if($isToday)
                                <span class="badge bg-danger rounded-pill">Hari Ini</span>
                            @elseif($isTomorrow)
                                <span class="badge bg-warning text-dark rounded-pill">Besok (H-1)</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $tglAcara->format('d M Y') }}</span>
                            @endif
                        </div>
                        @if(!empty($agenda->deskripsi))
                            <p class="text-muted small mb-0 mt-1">{{ \Illuminate\Support\Str::limit($agenda->deskripsi, 80) }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar2-x fs-2 d-block mb-2"></i>
                        <span class="small">Tidak ada agenda mendatang yang terjadwal.</span>
                    </div>
                @endforelse
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
        const monthlySeries = {!! json_encode($monthlySeries) !!};

        const colors = [
            '#10b981', // Pengaduan (Hijau)
            '#eab308', // Permintaan Info (Kuning)
            '#3b82f6', // Permohonan Dokumen (Biru)
            '#9333ea', // Peralihan UHC (Ungu)
            '#06b6d4', // Kegiatan PKRS (Cyan)
            '#ef4444', // Mobil LaSehat (Merah)
            '#6366f1'  // Agenda Logbook (Indigo)
        ];

        const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // 1. MULTI-LINE / STACKED BAR TREN BULANAN
        new Chart(document.getElementById('monthlyTrendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Pengaduan',
                        data: monthlySeries.pengaduan,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3
                    },
                    {
                        label: 'Permintaan Info',
                        data: monthlySeries.permintaan,
                        borderColor: '#eab308',
                        backgroundColor: 'rgba(234, 179, 8, 0.08)',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3
                    },
                    {
                        label: 'Mobil LaSehat',
                        data: monthlySeries.lasehat,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.08)',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3
                    },
                    {
                        label: 'Permohonan Dokumen',
                        data: monthlySeries.permohonan_info,
                        borderColor: '#3b82f6',
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 2.5
                    },
                    {
                        label: 'Peralihan UHC',
                        data: monthlySeries.uhc,
                        borderColor: '#9333ea',
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 2.5
                    },
                    {
                        label: 'Kegiatan PKRS',
                        data: monthlySeries.pkrs,
                        borderColor: '#06b6d4',
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 2.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } }
                    },
                    datalabels: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    }
                }
            }
        });

        // 2. BAR CHART PERBANDINGAN VOLUME MODUL
        new Chart(document.getElementById('mainBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Data',
                    data: dataValues,
                    backgroundColor: colors,
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end',
                        align: 'right',
                        color: '#334155',
                        font: { weight: '700', size: 11 },
                        formatter: (val) => val > 0 ? val : ''
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 11 } },
                        suggestedMax: Math.max(...dataValues, 5) * 1.15
                    },
                    y: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#334155', font: { size: 11, weight: '600' } }
                    }
                }
            }
        });

        // 3. DOUGHNUT CHART PROPORSI
        new Chart(document.getElementById('mainDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 12,
                            font: { family: "'Poppins', sans-serif", size: 11, color: '#475569' }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        font: { weight: '700', size: 11 },
                        formatter: (val, ctx) => {
                            let sum = ctx.chart._metasets[ctx.datasetIndex].total;
                            if (sum === 0 || val === 0) return '';
                            let pct = (val * 100 / sum);
                            return pct >= 4 ? pct.toFixed(0) + '%' : '';
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
