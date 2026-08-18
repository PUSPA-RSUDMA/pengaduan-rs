@extends('layouts.admin')

@section('title', 'Edit Permohonan Informasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Permohonan Informasi</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('permohonan-informasi.update', $permohonan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Pemohon</label>
                        <input type="text" name="nama_pemohon" class="form-control" value="{{ $permohonan->nama_pemohon }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Pasien</label>
                        <input type="text" name="nama_pasien" class="form-control" value="{{ $permohonan->nama_pasien }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $permohonan->no_hp }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="4" required>{{ $permohonan->keperluan }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Ganti Berkas Lampiran (Opsional)</label>
                        @if($permohonan->file_lampiran)
                            <div class="mb-2">
                                <a href="{{ Storage::disk('google')->url($permohonan->file_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-3">
                                    <i class="bi bi-cloud-arrow-down"></i> Lihat dari G-Drive
                                </a>
                            </div>
                        @endif
                        <input type="file" name="file_lampiran" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text small text-muted">Abaikan jika tidak ingin mengganti file. Format: PDF, JPG, PNG (Max: 10MB).</div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('permohonan-informasi.index') }}" class="btn btn-light border rounded-3 fw-medium">Kembali</a>
                        <button type="submit" class="btn btn-primary rounded-3 fw-medium"><i class="bi bi-save me-1"></i> Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection