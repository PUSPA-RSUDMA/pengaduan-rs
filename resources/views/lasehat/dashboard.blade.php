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

<div class="row g-4">
    {{-- Chart Ruangan --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="bi bi-building"></i> Ruangan Terbanyak Menggunakan LaSehat</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Ruangan</th><th>Total Order</th></tr></thead>
                    <tbody>
                        @forelse($ruanganStats as $r)
                            <tr><td>{{ $r->tempat_dirawat }}</td><td class="fw-bold text-primary">{{ $r->total }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart Supir --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold"><i class="bi bi-person-badge"></i> Supir Paling Aktif Bulan Ini</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Nama Supir</th><th>Total Jalan</th></tr></thead>
                    <tbody>
                        @forelse($supirStats as $s)
                            <tr><td>{{ $s->supir_ambulance }}</td><td class="fw-bold text-success">{{ $s->total }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center">Belum ada penugasan supir</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection