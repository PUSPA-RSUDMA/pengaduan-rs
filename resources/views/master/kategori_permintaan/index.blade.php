@extends('layouts.admin')

@section('title', 'Master Kategori Permintaan')

@section('content')
<style>
    /* Global Styles matching the modern dashboard reference */
    body {
        background-color: #f4f7f6;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .card-main { 
        border-radius: 16px !important; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; 
        border: 1px solid rgba(0,0,0,0.02) !important;
        background-color: #ffffff;
    }

    .card-kategori {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid #f3f4f6;
        border-top: 4px solid #10b981 !important; /* Emerald green accent */
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-kategori:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .text-title { color: #111827; font-weight: 700; font-size: 1.15rem; }
    
    /* Modern Button Styling */
    .btn-modern-primary {
        background-color: #10b981; /* Emerald green */
        color: white; border: none; border-radius: 8px;
        padding: 0.5rem 1rem; font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }
    
    .btn-action-icon {
        width: 28px; height: 28px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 6px; border: none; transition: 0.2s;
    }
    .btn-action-icon:hover { transform: scale(1.05); }

    /* Form & Modal Styling */
    .form-control-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem;
    }
    .form-control-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    
    .modal-content { border-radius: 16px; border: none; }
    .modal-header-modern { border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; background: white; border-radius: 16px 16px 0 0; }
    .modal-title-modern { font-weight: 700; color: #111827; font-size: 1.1rem; }
    .modal-footer-modern { border-top: 1px solid #f3f4f6; background: #f9fafb; border-radius: 0 0 16px 16px; }

    /* List Group Custom */
    .list-group-custom .list-group-item {
        border: none;
        border-bottom: 1px solid #f3f4f6;
        background: transparent;
        transition: 0.2s;
    }
    .list-group-custom .list-group-item:last-child { border-bottom: none; }
    .list-group-custom .list-group-item:hover {
        background-color: #f8fafc !important;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-12">
        
        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-main mb-4">
            <div class="card-body p-4 p-md-5">
                
                {{-- HEADER SECTION --}}
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-5 gap-3">
                    <div>
                        <h5 class="text-title m-0"><i class="bi bi-info-square text-primary me-2"></i>Master Kategori & Detail Permintaan (Informasi)</h5>
                        <small class="text-muted">Kelola struktur kategori dan daftar spesifik permintaan informasi</small>
                    </div>
                    <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
                    </button>
                </div>

                {{-- KATEGORI GRID SECTION --}}
                <div class="row g-4">
                    @forelse($kategoris as $kategori)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 card-kategori bg-white">
                            
                            {{-- HEADER KATEGORI --}}
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark m-0">{{ $kategori->name }}</h6>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-action-icon bg-warning-subtle text-warning" data-bs-toggle="modal" data-bs-target="#modalEditCat{{ $kategori->id }}" title="Edit Kategori">
                                        <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                    </button>
                                    <form action="{{ route('kategori-permintaan.destroy', $kategori->id) }}" method="POST" onsubmit="return confirm('Hapus seluruh kategori ini beserta detailnya?')" class="m-0">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-action-icon bg-danger-subtle text-danger" title="Hapus Kategori">
                                            <i class="bi bi-trash-fill" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            {{-- BODY: SUB-ITEM KATEGORI --}}
                            <div class="card-body p-0 mt-2">
                                <ul class="list-group list-group-flush list-group-custom small w-100">
                                    @forelse($kategori->items as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-2">
                                        <span class="text-dark fw-medium">{{ $item->name }}</span>
                                        <div class="d-flex align-items-center gap-1 opacity-75 hover-opacity-100">
                                            {{-- Tombol Edit Sub-Item --}}
                                            <button type="button" class="btn btn-link text-warning p-1 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalEditItem{{ $item->id }}" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            {{-- Tombol Hapus Sub-Item --}}
                                            <form action="{{ route('kategori-permintaan.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pilihan ini?')" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-1 text-decoration-none" title="Hapus">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>

                                    {{-- Modal Edit Sub-Item --}}
                                    <div class="modal fade" id="modalEditItem{{ $item->id }}" tabindex="-1" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content shadow-lg">
                                                <div class="modal-header-modern p-3 d-flex justify-content-between align-items-center">
                                                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-pencil-square text-warning me-1"></i> Edit Pilihan</h6>
                                                    <button type="button" class="btn-close" style="font-size: 0.7rem;" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('kategori-permintaan.items.update', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-3 bg-light">
                                                        <label class="small fw-bold text-muted mb-1">Nama Pilihan</label>
                                                        <input type="text" name="name" class="form-control form-control-modern form-control-sm" value="{{ $item->name }}" required>
                                                    </div>
                                                    <div class="modal-footer-modern p-2 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-light btn-sm px-3 border shadow-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning text-dark btn-sm fw-bold px-3 shadow-sm rounded-3">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <li class="list-group-item text-muted text-center" style="font-size: 0.8rem; padding: 1rem 0;">
                                        <i class="bi bi-inbox opacity-50 d-block mb-1 fs-5"></i> Belum ada detail pilihan.
                                    </li>
                                    @endforelse
                                </ul>
                            </div>
                            
                            {{-- FOOTER: FORM TAMBAH SUB-ITEM --}}
                            <div class="card-footer bg-light p-3 border-top-0" style="border-radius: 0 0 12px 12px;">
                                <form action="{{ route('kategori-permintaan.items.store') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="kategori_permintaan_id" value="{{ $kategori->id }}">
                                    <input type="text" name="name" class="form-control form-control-modern form-control-sm bg-white" placeholder="Ketik pilihan detail baru..." required>
                                    <button type="submit" class="btn btn-modern-primary btn-sm px-3 shadow-sm d-flex align-items-center justify-content-center">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Edit Kategori --}}
                    <div class="modal fade text-start" id="modalEditCat{{ $kategori->id }}" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                    <h6 class="modal-title-modern m-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Kategori Permintaan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('kategori-permintaan.update', $kategori->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 bg-light">
                                        <div class="card border-0 shadow-sm p-4">
                                            <label class="form-label fw-bold text-muted small mb-1">Nama Kategori <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control form-control-modern" value="{{ $kategori->name }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-modern shadow-sm px-4" style="border-radius: 8px;">Update Kategori</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5 rounded-4 bg-light border border-light">
                            <i class="bi bi-info-square fs-1 text-secondary mb-3 d-block opacity-50"></i>
                            <h6 class="text-muted fw-bold">Belum ada Kategori Permintaan (Informasi).</h6>
                            <p class="text-muted small">Silakan tambah kategori baru melalui tombol di kanan atas.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Kategori Baru --}}
<div class="modal fade" id="modalCreateCategory" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-journal-plus text-primary me-2"></i>Tambah Kategori Permintaan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kategori-permintaan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-4">
                        <label class="form-label fw-bold text-muted small mb-1">Nama Kategori Induk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-modern" placeholder="Contoh: Informasi Jadwal Dokter..." required>
                    </div>
                </div>
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection