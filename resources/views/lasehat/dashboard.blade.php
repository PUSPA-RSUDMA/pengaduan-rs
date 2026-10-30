@extends('layouts.admin')
@section('title', 'Dashboard LaSehat')
@section('content')

<style>
    /* Global Styles matching the modern dashboard reference */
    body {
        background-color: #f4f7f6;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .card { 
        border-radius: 16px !important; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; 
        border: 1px solid rgba(0,0,0,0.02) !important;
        background-color: #ffffff;
    }

    .text-title { color: #111827; font-weight: 700; font-size: 1.15rem; }
    
    /* Modern Button Styling */
    .btn-modern-primary {
        background-color: #10b981; /* Emerald green */
        color: white; border: none; border-radius: 8px;
        font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }

    /* Modern Table Styling */
    .table-modern { margin-bottom: 0; }
    .table-modern > :not(caption) > * > * {
        padding: 1rem 1.5rem; border-bottom-color: #f3f4f6;
        vertical-align: middle; background-color: transparent !important;
    }
    .table-modern thead th {
        text-transform: uppercase; font-size: 0.75rem; font-weight: 600;
        color: #6b7280; border-bottom: 2px solid #f3f4f6 !important; letter-spacing: 0.5px;
    }
    .table-modern tbody tr:hover { background-color: #f8fafc !important; }
    
    /* Tfoot modern styling */
    .table-modern tfoot td {
        background-color: #f9fafb !important;
        border-top: 2px solid #e5e7eb !important;
        border-bottom: none !important;
        font-size: 0.9rem;
    }

    /* Soft Badges */
    .badge-soft { border-radius: 20px; padding: 0.4rem 0.9rem; font-weight: 700; font-size: 0.75rem; }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

    /* Form Styling */
    .form-control-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.4rem 0.75rem;
    }
    .form-control-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    .filter-wrapper {
        background-color: #ffffff; border-radius: 12px; border: 1px solid #f3f4f6;
    }
</style>

{{-- HEADER SECTION --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h5 class="text-title m-0"><i class="bi bi-ambulance text-danger me-2"></i>Dashboard LaSehat</h5>
        <small class="text-muted">Rekapitulasi data ruangan dan aktivitas supir</small>
    </div>
    
    {{-- Filter Range Tanggal (Update) --}}
    <div class="filter-wrapper shadow-sm p-2">
        <form method="GET" action="{{ route('lasehat.dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center m-0">
            <div class="d-flex align-items-center gap-2">
                <input type="date" name="start_date" class="form-control form-control-modern" value="{{ $startDate }}" required>
                <span class="text-muted small fw-bold">s/d</span>
                <input type="date" name="end_date" class="form-control form-control-modern" value="{{ $endDate }}" required>
            </div>
            <button type="submit" class="btn btn-modern-primary btn-sm px-3 py-2">
                <i class="bi bi-search me-1"></i> Filter
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    {{-- Tabel Statistik Ruangan --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0">
            <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-hospital text-primary me-2"></i> Statistik Ruangan Terbanyak</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>Ruangan</th>
                                <th class="text-center" width="35%">Total Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ruanganStats as $stat)
                            <tr>
                                <td class="fw-bold text-dark">{{ $stat->tempat_dirawat }}</td>
                                <td class="text-center">
                                    <span class="badge-soft badge-soft-primary">{{ $stat->total }} Order</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-folder2-open fs-2 text-light mb-2"></i>
                                        <span>Tidak ada data pada rentang tanggal ini.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($ruanganStats->isNotEmpty())
                        <tfoot class="fw-bold">
                            <tr>
                                <td class="text-secondary text-end pe-4">Total Keseluruhan Order :</td>
                                <td class="text-center text-primary fs-6">{{ $totalOrderRuangan }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Supir Paling Aktif --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0">
            <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-person-badge text-success me-2"></i> Supir Paling Aktif</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Supir</th>
                                <th class="text-center" width="35%">Total Jalan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supirStats as $stat)
                            <tr>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        {{ $stat->supir_ambulance }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-soft badge-soft-success">{{ $stat->total }} Jalan</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-folder2-open fs-2 text-light mb-2"></i>
                                        <span>Tidak ada data supir pada rentang tanggal ini.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($supirStats->isNotEmpty())
                        <tfoot class="fw-bold">
                            <tr>
                                <td class="text-secondary text-end pe-4">Total Keseluruhan Jalan :</td>
                                <td class="text-center text-success fs-6">{{ $totalJalanSupir }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection