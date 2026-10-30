@extends('layouts.admin')

@section('title', 'Edit Permohonan Informasi')

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
        padding: 0.5rem 1.25rem; font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }

    /* Form Styling */
    .form-control-modern, .form-select-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.6rem 0.75rem;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }

    /* Custom File Input override */
    .form-control-modern[type="file"] { padding: 0.35rem 0.75rem; }
    .form-control-modern::file-selector-button {
        background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px;
        color: #374151; font-weight: 600; padding: 0.3rem 0.75rem; margin-right: 1rem;
        transition: all 0.2s; cursor: pointer;
    }
    .form-control-modern::file-selector-button:hover { background-color: #e5e7eb; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        
        {{-- ALERTS / ERRORS --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fee2e2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal Memperbarui Data:</strong>
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
            <div class="card-body p-4 p-md-5">
                
                {{-- HEADER SECTION --}}
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h5 class="text-title m-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Permohonan Informasi</h5>
                        <small class="text-muted">Perbarui data informasi pemohon dan berkas lampiran</small>
                    </div>
                </div>

                <form action="{{ route('permohonan-informasi.update', $permohonan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Nama Pemohon <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemohon" class="form-control form-control-modern" value="{{ $permohonan->nama_pemohon }}" required placeholder="Nama lengkap pemohon">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Nama Pasien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pasien" class="form-control form-control-modern" value="{{ $permohonan->nama_pasien }}" required placeholder="Nama lengkap pasien">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small mb-1">No. HP / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border: 1px solid #e5e7eb;"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="no_hp" class="form-control form-control-modern border-start-0" style="border-radius: 0 8px 8px 0;" value="{{ $permohonan->no_hp }}" required placeholder="Contoh: 628123456789">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small mb-1">Keperluan <span class="text-danger">*</span></label>
                            <textarea name="keperluan" class="form-control form-control-modern" rows="4" required placeholder="Jelaskan keperluan permohonan informasi...">{{ $permohonan->keperluan }}</textarea>
                        </div>

                        <div class="col-md-12 border-top pt-4">
                            <label class="form-label fw-bold text-muted small mb-1">Ganti Berkas Lampiran (Opsional)</label>
                            @if($permohonan->file_lampiran)
                                <div class="mb-3">
                                    <a href="{{ Storage::disk('google')->url($permohonan->file_lampiran) }}" target="_blank" class="btn btn-sm btn-light border text-info rounded-pill fw-bold shadow-sm py-1 px-3" style="font-size: 0.8rem;">
                                        <i class="bi bi-cloud-arrow-down-fill me-1"></i> Lihat Berkas Saat Ini di G-Drive
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="file_lampiran" class="form-control form-control-modern" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text small text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Abaikan jika tidak ingin mengganti file. Format: PDF, JPG, PNG (Maks: 10MB).</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <a href="{{ route('permohonan-informasi.index') }}" class="btn btn-light border shadow-sm px-4 py-2" style="border-radius: 8px; font-weight: 600;">Kembali</a>
                        <button type="submit" class="btn btn-modern-primary px-4 py-2 shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Data
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection