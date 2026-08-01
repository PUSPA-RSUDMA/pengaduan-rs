@extends('layouts.admin')
@section('title', 'Dashboard LaSehat')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h4 class="fw-bold"><i class="bi bi-ambulance text-danger me-2"></i>Dashboard LaSehat</h4>
    
    {{-- Filter Range Tanggal (Update) --}}
    <form method="GET" action="{{ route('lasehat.dashboard') }}" class="d-flex gap-2 mt-2 mt-md-0 align-items-center">
        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" required>
        <span class="fw-bold text-muted">s/d</span>
        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" required>
        <button type="submit" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-search"></i> Filter
        </button>
    </form>
</div>

<div class="row">
    {{-- Tabel Statistik Ruangan --}}
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-hospital text-primary me-2"></i> Statistik Ruangan Terbanyak
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ruangan</th>
                                <th class="text-center" width="30%">Total Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ruanganStats as $stat)
                            <tr>
                                <td class="fw-semibold">{{ $stat->tempat_dirawat }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $stat->total }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Tidak ada data pada rentang tanggal ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($ruanganStats->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total Keseluruhan Order</td>
                                <td class="text-center text-primary">{{ $totalOrderRuangan }}</td>
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-person-badge text-success me-2"></i> Supir Paling Aktif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Supir</th>
                                <th class="text-center" width="30%">Total Jalan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supirStats as $stat)
                            <tr>
                                <td class="fw-semibold">{{ $stat->supir_ambulance }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $stat->total }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Tidak ada data supir pada rentang tanggal ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($supirStats->isNotEmpty())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total Keseluruhan Jalan Supir</td>
                                <td class="text-center text-success">{{ $totalJalanSupir }}</td>
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