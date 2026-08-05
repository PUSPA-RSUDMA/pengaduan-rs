@extends('layouts.admin')
@section('title', 'Master Kategori Keluhan')
@section('content')
<style>
    .list-group-item:hover {
        background-color: #f8f9fa !important; /* Warna background saat di-hover (opsional) */
        color: #212529 !important;           /* Memaksa warna teks tetap gelap */
    }
    .list-group-item:hover span {
        color: #212529 !important;           /* Memaksa elemen teks di dalamnya tetap gelap */
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Master Kategori & Detail Keluhan</h5>
        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">
            <i class="bi bi-plus-lg"></i> Tambah Kategori Baru
        </button>
    </div>
    <div class="card-body">
        
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        
        <div class="row g-4">
            @foreach($kategoris as $kategori)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 border-top border-primary border-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold">{{ $kategori->name }}</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light text-warning border" data-bs-toggle="modal" data-bs-target="#modalEditCat{{ $kategori->id }}"><i class="bi bi-pencil"></i></button>
                            <form action="{{ route('kategori-keluhan.destroy', $kategori->id) }}" method="POST" onsubmit="return confirm('Hapus seluruh kategori ini?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-light text-danger border"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                        @forelse($kategori->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <span>{{ $item->name }}</span>
                            <div class="d-flex align-items-center gap-2">
                                {{-- Tombol Edit --}}
                                <button type="button" class="btn btn-link text-warning p-0 m-0" data-bs-toggle="modal" data-bs-target="#modalEditItem{{ $item->id }}" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('kategori-permintaan.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pilihan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0" title="Hapus"><i class="bi bi-x-circle-fill"></i></button>
                                </form>
                            </div>
                        </li>

                        {{-- Modal Edit Sub-Item (Berada di dalam loop agar id-nya unik) --}}
                        <div class="modal fade" id="modalEditItem{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning py-2">
                                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-1"></i> Edit Pilihan</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    {{-- PERHATIAN: Ubah route menjadi 'kategori-keluhan.items.update' jika Anda sedang mengedit file Kategori Keluhan --}}
                                    <form action="{{ route('kategori-permintaan.items.update', $item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-3">
                                            <label class="small fw-bold mb-1">Nama Pilihan (Sub Kategori)</label>
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $item->name }}" required>
                                        </div>
                                        <div class="modal-footer p-2 bg-light border-top">
                                            <button type="submit" class="btn btn-warning btn-sm fw-bold">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <li class="list-group-item text-muted fst-italic text-center">Belum ada pilihan.</li>
                        @endforelse
                        </ul>
                    </div>
                    <div class="card-footer bg-light p-2">
                        <form action="{{ route('kategori-keluhan.items.store') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="kategori_keluhan_id" value="{{ $kategori->id }}">
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Ketik pilihan baru..." required>
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-plus"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Edit Kategori --}}
            <div class="modal fade" id="modalEditCat{{ $kategori->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('kategori-keluhan.update', $kategori->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <label>Nama Kategori</label>
                                <input type="text" name="name" class="form-control" value="{{ $kategori->name }}" required>
                            </div>
                            <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Modal Tambah Kategori --}}
<div class="modal fade" id="modalCreateCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kategori-keluhan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: IT / Sistem" required>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection