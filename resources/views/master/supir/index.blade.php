@extends('layouts.admin')
@section('title', 'Data Master Supir')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0"><i class="bi bi-person-bounding-box text-primary me-2"></i>Kelola Master Supir</h5>
</div>

<div class="row">
    {{-- Form Tambah Supir --}}
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-plus-circle me-2"></i> Tambah Supir Baru
            </div>
            <div class="card-body">
                <form action="{{ route('master.supir.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Supir Ambulance</label>
                        <input type="text" name="nama_supir" class="form-control @error('nama_supir') is-invalid @enderror" required placeholder="Contoh: Syam">
                        @error('nama_supir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Supir --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-list-ul me-2"></i> Daftar Supir
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="10%" class="text-center">No</th>
                                <th>Nama Supir</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supirs as $index => $supir)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $supir->nama_supir }}</td>
                                <td class="text-center">
                                    <form action="{{ route('master.supir.destroy', $supir->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supir ini dari sistem?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada data supir. Silakan tambahkan di form sebelah kiri.</td>
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