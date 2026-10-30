@extends('layouts.admin')

@section('title', 'Media Pengaduan')

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
        padding: 0.5rem 1rem; font-weight: 600; transition: all 0.2s;
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
</style>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        
        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fee2e2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal Menyimpan Data:</strong>
                <ul class="mb-0 mt-1 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 mb-4">
            <div class="card-body p-4">
                
                {{-- HEADER SECTION --}}
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                    <div>
                        <h5 class="text-title m-0"><i class="bi bi-chat-square-text text-primary me-2"></i>Media Pengaduan</h5>
                        <small class="text-muted">Kelola platform atau media masuknya aduan</small>
                    </div>
                    
                    {{-- TOMBOL TAMBAH --}}
                    <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Baru
                    </button>
                </div>

                {{-- TABLE SECTION --}}
                <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
                    <table class="table table-modern align-middle table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="10%">No</th>
                                <th>Nama Media</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sources as $source)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="text-start fw-bold text-dark">{{ $source->name }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- TOMBOL EDIT --}}
                                        <button type="button" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $source->id }}" title="Edit Media">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        
                                        {{-- TOMBOL HAPUS --}}
                                        <form action="{{ route('sources.destroy', $source->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus Media">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- ========================================== --}}
                                    {{-- MODAL EDIT (Tanpa modal-dialog-centered) --}}
                                    {{-- ========================================== --}}
                                    <div class="modal fade" id="modalEdit{{ $source->id }}" tabindex="-1" data-bs-backdrop="static">
                                        <div class="modal-dialog"> 
                                            <div class="modal-content shadow-lg">
                                                <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                                    <h6 class="modal-title-modern m-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Media</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('sources.update', $source->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 bg-light">
                                                        <div class="card border-0 shadow-sm p-4">
                                                            <label class="form-label fw-bold text-muted small mb-1">Nama Media <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control form-control-modern" value="{{ $source->name }}" required placeholder="Contoh: WhatsApp, Instagram...">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-modern shadow-sm px-4" style="border-radius: 8px;">Update Media</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END MODAL EDIT --}}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-chat-square-text fs-1 text-light mb-2"></i>
                                        <span>Data media pengaduan masih kosong.</span>
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

{{-- ========================================== --}}
{{-- MODAL CREATE (Tanpa modal-dialog-centered) --}}
{{-- ========================================== --}}
<div class="modal fade" id="modalCreate" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-journal-plus text-primary me-2"></i>Tambah Media Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sources.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-4">
                        <label class="form-label fw-bold text-muted small mb-1">Nama Media <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-modern" placeholder="Contoh: WhatsApp, Instagram..." required>
                    </div>
                </div>
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-4" style="border-radius: 8px; font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection