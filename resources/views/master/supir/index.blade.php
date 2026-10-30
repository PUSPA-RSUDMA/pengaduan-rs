@extends('layouts.admin')
@section('title', 'Data Master Supir')
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
        padding: 0.6rem 1rem; font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }
    
    .btn-action-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: none; transition: 0.2s;
    }
    .btn-action-icon:hover { transform: scale(1.05); }

    /* Modern Table Styling */
    .table-modern { margin-bottom: 0; }
    .table-modern > :not(caption) > * > * {
        padding: 1rem 1rem; border-bottom-color: #f3f4f6;
        vertical-align: middle; background-color: transparent !important;
    }
    .table-modern thead th {
        text-transform: uppercase; font-size: 0.75rem; font-weight: 600;
        color: #6b7280; border-bottom: 2px solid #f3f4f6 !important; letter-spacing: 0.5px;
    }
    .table-modern tbody tr:hover { background-color: #f8fafc !important; }

    /* Form Styling */
    .form-control-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.9rem; padding: 0.6rem 0.8rem;
    }
    .form-control-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
</style>

{{-- HEADER SECTION --}}
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
    <div>
        <h5 class="text-title m-0"><i class="bi bi-person-bounding-box text-primary me-2"></i>Kelola Master Supir</h5>
        <small class="text-muted">Manajemen data supir ambulance LaSehat</small>
    </div>
</div>

<div class="row g-4">
    {{-- Form Tambah Supir --}}
    <div class="col-lg-4 col-md-5">
        <div class="card border-0 h-100">
            <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Supir Baru</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('master.supir.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small mb-2">Nama Supir Ambulance <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supir" class="form-control form-control-modern @error('nama_supir') is-invalid border-danger @enderror" required placeholder="Contoh: Syam, Budi...">
                        @error('nama_supir')
                            <div class="invalid-feedback small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-modern-primary w-100 shadow-sm d-flex justify-content-center align-items-center">
                        <i class="bi bi-save me-2"></i> Simpan Data Supir
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Supir --}}
    <div class="col-lg-8 col-md-7">
        <div class="card border-0 h-100">
            <div class="card-header bg-white pt-4 pb-2 px-4 border-bottom-0">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-list-ul text-success me-2"></i>Daftar Supir Terdaftar</h6>
            </div>
            <div class="card-body p-4">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
                    <table class="table table-modern align-middle table-hover m-0">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="10%">No</th>
                                <th class="text-start">Nama Supir</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supirs as $index => $supir)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary" style="width: 36px; height: 36px;">
                                            <i class="bi bi-person-fill fs-5"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $supir->nama_supir }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('master.supir.destroy', $supir->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supir ini dari sistem?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus Supir">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-person-x fs-1 text-light mb-2"></i>
                                        <span class="fw-bold">Belum ada data supir.</span>
                                        <small>Silakan tambahkan melalui form di samping.</small>
                                    </div>
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

@endsection