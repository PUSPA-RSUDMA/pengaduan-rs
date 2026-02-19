@extends('layouts.admin')

@section('title', 'Master Kegawatan')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Level Kegawatan</h6>
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
            <thead class="table-light text-center">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Level</th>
                    <th width="15%">Warna (Pixel)</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grades as $g)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $g->name }}</td>
                    
                    {{-- LOGIKA TAMPILAN WARNA (Support Hex Code & Class Lama) --}}
                    <td class="text-center">
                        @php
                            // Cek apakah datanya berupa Kode Hex (#ff0000) atau Class Lama (bg-danger)
                            $isHex = Str::startsWith($g->color_class, '#');
                        @endphp
                        
                        <span class="badge rounded-circle border border-secondary p-2 shadow-sm {{ $isHex ? '' : $g->color_class }}" 
                              style="{{ $isHex ? 'background-color: '.$g->color_class : '' }}; width: 25px; height: 25px; display: inline-block;"
                              title="{{ $g->name }}">
                        </span>
                    </td>

                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editModal{{ $g->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('master.grades.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade text-start" id="editModal{{ $g->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Level</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('master.grades.update', $g->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama Level</label>
                                                <input type="text" name="name" class="form-control" value="{{ $g->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pilih Warna</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    {{-- INPUT COLOR PICKER --}}
                                                    <input type="color" name="color_class" class="form-control form-control-color" 
                                                           value="{{ Str::startsWith($g->color_class, '#') ? $g->color_class : '#563d7c' }}" 
                                                           title="Klik kotak ini untuk memilih warna">
                                                    <small class="text-muted">Klik kotak warna untuk mengganti.</small>
                                                </div>
                                            </div>
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
                <tr><td colspan="4" class="text-center">Kosong.</td></tr>
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
                <h5 class="modal-title">Tambah Level Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('master.grades.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">Nama Level</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Sangat Gawat" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Pilih Warna</label>
                        <div class="d-flex align-items-center gap-2">
                            {{-- INPUT COLOR PICKER --}}
                            <input type="color" name="color_class" class="form-control form-control-color" value="#ff0000" title="Pilih Warna">
                            <small class="text-muted">Klik kotak untuk memilih warna bebas.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection