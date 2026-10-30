@extends('layouts.admin')

@section('title', 'Data Peralihan UHC')

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

    /* Soft Badges */
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

    /* Form Styling */
    .form-control-modern, .form-select-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }

    /* Modal Styling */
    .modal-content { border-radius: 16px; border: none; }
    .modal-header-modern { border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; background: white; border-radius: 16px 16px 0 0; }
    .modal-title-modern { font-weight: 700; color: #111827; font-size: 1.1rem; }
    .modal-footer-modern { border-top: 1px solid #f3f4f6; background: #f9fafb; border-radius: 0 0 16px 16px; }

    /* Custom File Input override */
    .form-control-modern[type="file"] { padding: 0.35rem 0.75rem; }
    .form-control-modern::file-selector-button {
        background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px;
        color: #374151; font-weight: 600; padding: 0.25rem 0.75rem; margin-right: 1rem;
        transition: all 0.2s; cursor: pointer;
    }
    .form-control-modern::file-selector-button:hover { background-color: #e5e7eb; }
</style>

<div class="row">
    <div class="col-12">
        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px; background-color: #fee2e2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal Menyimpan Data:</strong>
                <ul class="mb-0 mt-1 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- MAIN CARD --}}
        <div class="card border-0 mb-4">
            <div class="card-body p-4">
                
                {{-- HEADER SECTION --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h5 class="text-title m-0"><i class="bi bi-hospital text-primary me-2"></i>Daftar Peralihan UHC</h5>
                        <small class="text-muted">Kelola data peralihan segmen kepesertaan ke UHC</small>
                    </div>
                    
                    <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">   
                        <i class="bi bi-plus-lg me-1"></i> Tambah Data
                    </button>
                </div>

                {{-- TABLE SECTION --}}
                <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
                    <table class="table table-modern align-middle table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Pasien</th>
                                <th>No. HP</th>
                                <th>Segmen Sebelumnya</th>
                                <th>Alasan Peralihan</th>
                                <th class="text-center">Lampiran</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse($data as $item)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->created_at->format('d M Y') }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $item->nama_pemohon }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary" style="font-size: 0.85rem;">{{ $item->nama_pasien }}</span>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/{{ $item->no_hp }}" target="_blank" class="badge-soft badge-soft-success text-decoration-none d-inline-flex align-items-center">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $item->no_hp }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-soft badge-soft-info">{{ $item->segmen_kepesertaan }}</span>
                                    </td>
                                    <td class="text-start">
                                        <span class="text-muted" style="font-size: 0.85rem;">{{ Str::limit($item->alasan_peralihan, 40) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->file_lampiran)
                                            <a href="{{ Storage::disk('google')->url($item->file_lampiran) }}" target="_blank" class="btn btn-sm btn-light border text-info rounded-pill fw-bold shadow-sm py-1 px-3" style="font-size: 0.75rem;">
                                                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Lihat
                                            </a>
                                        @else
                                            <span class="badge-soft badge-soft-secondary">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('permohonan-uhc.edit', $item->id) }}" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <form action="{{ route('permohonan-uhc.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? File lampiran juga akan terhapus permanen.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted d-flex flex-column align-items-center">
                                            <i class="bi bi-folder2-open fs-1 text-light mb-2"></i>
                                            <span>Belum ada data Peralihan UHC.</span>
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

{{-- MODAL TAMBAH DATA --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg">
            
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0" id="modalTambahLabel">
                    <i class="bi bi-journal-plus text-primary me-2"></i>Tambah Peralihan UHC Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('permohonan-uhc.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small mb-1">Nama Pemohon <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pemohon" class="form-control form-control-modern" required placeholder="Masukkan nama pemohon">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small mb-1">Nama Pasien <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pasien" class="form-control form-control-modern" required placeholder="Masukkan nama pasien">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small mb-1">No. HP / WhatsApp <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border: 1px solid #e5e7eb;"><i class="bi bi-telephone"></i></span>
                                    <input type="number" name="no_hp" class="form-control form-control-modern border-start-0" style="border-radius: 0 8px 8px 0;" required placeholder="Contoh: 628123456789">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small mb-1">Segmen Kepesertaan Sebelumnya <span class="text-danger">*</span></label>
                                <input type="text" name="segmen_kepesertaan" class="form-control form-control-modern" required placeholder="Contoh: Mandiri / PBPU / PBI APBN">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small mb-1">Alasan Peralihan <span class="text-danger">*</span></label>
                                <textarea name="alasan_peralihan" class="form-control form-control-modern" rows="3" required placeholder="Jelaskan alasan beralih ke UHC..."></textarea>
                            </div>
                            
                            <div class="col-md-12 border-top pt-3 mt-2">
                                <label class="form-label fw-bold text-muted small mb-1">Upload Berkas Lampiran <span class="text-danger">*</span></label>
                                <input type="file" name="file_lampiran" class="form-control form-control-modern" required accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Format file: PDF, JPG, PNG. Maksimal ukuran: 10MB.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection