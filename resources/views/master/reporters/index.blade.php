@extends('layouts.admin')

@section('title', 'Master Unit Pelapor')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Unit Pelapor</h6>
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg"></i> Tambah Baru
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <table class="table table-bordered table-hover align-middle">
            {{-- PERUBAHAN: text-center align-middle pada THEAD agar judul kolom RATA TENGAH --}}
            <thead class="table-light text-center align-middle">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Unit Pelapor</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reporters as $r)
                <tr>
                    {{-- No: Rata Tengah --}}
                    <td class="text-center">{{ $loop->iteration }}</td>
                    
                    {{-- Nama: Rata Kiri (Default) --}}
                    <td>{{ $r->name }}</td>
                    
                    {{-- Aksi: Rata Tengah --}}
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editModal{{ $r->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('master.reporters.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade text-start" id="editModal{{ $r->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Pelapor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('master.reporters.update', $r->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body text-start">
                                            <label class="form-label fw-bold">Nama Unit Pelapor</label>
                                            <input type="text" name="name" class="form-control" value="{{ $r->name }}" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-3">Data masih kosong.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pelapor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('master.reporters.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-bold">Nama Unit Pelapor</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection