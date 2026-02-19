@extends('layouts.admin')

@section('title', 'Media Pengaduan')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Media Pengaduan</h5>
        
        {{-- TOMBOL TAMBAH --}}
        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bi bi-plus-lg"></i> Tambah Baru
        </button>
    </div>
    <div class="card-body">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Media</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sources as $source)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $source->name }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                
                                <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $source->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <form action="{{ route('sources.destroy', $source->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- ========================================== --}}
                    {{-- MODAL EDIT (Posisi Default / Agak ke Atas) --}}
                    {{-- ========================================== --}}
                    <div class="modal fade" id="modalEdit{{ $source->id }}" tabindex="-1" data-bs-backdrop="static">
                        {{-- REVISI: Saya HAPUS 'modal-dialog-centered' biar posisinya naik ke atas --}}
                        <div class="modal-dialog"> 
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Media</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('sources.update', $source->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nama Media</label>
                                            <input type="text" name="name" class="form-control" value="{{ $source->name }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL EDIT --}}

                    @empty
                    <tr><td colspan="3" class="text-center">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL CREATE (Posisi Default / Agak ke Atas) --}}
{{-- ========================================== --}}
<div class="modal fade" id="modalCreate" tabindex="-1" data-bs-backdrop="static">
    {{-- REVISI: Saya HAPUS 'modal-dialog-centered' biar posisinya naik ke atas --}}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Media Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sources.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Media</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: WhatsApp, Instagram..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection