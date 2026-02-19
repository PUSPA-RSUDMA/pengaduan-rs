@extends('layouts.admin')

@section('title', 'Edit Data Pengaduan')

@section('content')
<div class="container-fluid p-0" style="max-width: 900px;">
    
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Pengaduan</h6>
            <span class="badge bg-white text-dark shadow-sm">Tiket: {{ $complaint->ticket_code }}</span>
        </div>
        
        <div class="card-body p-4 bg-light">
            <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Identitas & Waktu</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Waktu Laporan Masuk</label>
                                <input type="date" name="created_at" class="form-control" value="{{ $complaint->created_at->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Unit Pelapor</label>
                                <select name="reporter_type" class="form-select">
                                    <option value="Pasien/Keluarga" {{ $complaint->reporter_type == 'Pasien/Keluarga' ? 'selected' : '' }}>Pasien/Keluarga</option>
                                    <option value="Wartawan/Jurnalis" {{ $complaint->reporter_type == 'Wartawan/Jurnalis' ? 'selected' : '' }}>Wartawan/Jurnalis</option>
                                    <option value="LSM" {{ $complaint->reporter_type == 'LSM' ? 'selected' : '' }}>LSM</option>
                                    <option value="Masyarakat Umum" {{ $complaint->reporter_type == 'Masyarakat Umum' ? 'selected' : '' }}>Masyarakat Umum</option>
                                    <option value="Internal RS" {{ $complaint->reporter_type == 'Internal RS' ? 'selected' : '' }}>Internal RS</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">Nama Pelapor</label>
                                <input type="text" name="reporter_name" class="form-control" value="{{ $complaint->reporter_name }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">2. Detail Keluhan</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Media</label>
                                <select name="source_id" class="form-select">
                                    @foreach($sources as $source)
                                        <option value="{{ $source->id }}" {{ $complaint->source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Unit Tujuan</label>
                                <input type="text" name="unit_destination" class="form-control" value="{{ $complaint->unit_destination }}">
                            </div>
                        </div>

                        <div class="mb-4 text-center">
                            <label class="small fw-bold text-muted d-block mb-2">Grade / Tingkat Kegawatan</label>
                            <div class="btn-group gap-3" role="group">
                                <input type="radio" class="btn-check" name="grade" id="edR" value="Merah" {{ $complaint->grade == 'Merah' ? 'checked' : '' }}>
                                <label class="btn btn-outline-light border-0 shadow-sm p-0 rounded-circle" for="edR" style="width: 50px; height: 50px; overflow: hidden;">
                                    <div class="w-100 h-100 bg-danger d-flex align-items-center justify-content-center text-white fw-bold fs-4">
                                        <i class="bi bi-check-lg d-none"></i>
                                    </div>
                                </label>

                                <input type="radio" class="btn-check" name="grade" id="edY" value="Kuning" {{ $complaint->grade == 'Kuning' ? 'checked' : '' }}>
                                <label class="btn btn-outline-light border-0 shadow-sm p-0 rounded-circle" for="edY" style="width: 50px; height: 50px; overflow: hidden;">
                                    <div class="w-100 h-100 bg-warning text-dark d-flex align-items-center justify-content-center fw-bold fs-4">
                                        <i class="bi bi-check-lg d-none"></i>
                                    </div>
                                </label>

                                <input type="radio" class="btn-check" name="grade" id="edG" value="Hijau" {{ $complaint->grade == 'Hijau' ? 'checked' : '' }}>
                                <label class="btn btn-outline-light border-0 shadow-sm p-0 rounded-circle" for="edG" style="width: 50px; height: 50px; overflow: hidden;">
                                    <div class="w-100 h-100 bg-success text-white d-flex align-items-center justify-content-center fw-bold fs-4">
                                        <i class="bi bi-check-lg d-none"></i>
                                    </div>
                                </label>
                            </div>
                            <div class="small text-muted mt-2">Merah (Gawat) • Kuning (Sedang) • Hijau (Ringan)</div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Isi Keluhan</label>
                            <textarea name="description" class="form-control" rows="4">{{ $complaint->description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-warning bg-opacity-10 rounded">
                        <h6 class="fw-bold text-dark mb-3">3. Proses & Penyelesaian</h6>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Tgl Verifikasi (Petugas)</label>
                                <input type="date" name="date" class="form-control" value="{{ $complaint->date }}">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Status Akhir</label>
                                <select name="status" class="form-select fw-bold border-warning">
                                    <option value="Pending" {{ $complaint->status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="Proses" {{ $complaint->status == 'Proses' ? 'selected' : '' }}>🔄 Proses</option>
                                    <option value="Selesai" {{ $complaint->status == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('complaints.index') }}" class="btn btn-light border px-4 rounded-pill">Kembali</a>
                    <button type="submit" class="btn btn-warning px-5 fw-bold text-dark rounded-pill shadow-sm">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-check:checked + label .bi-check-lg { display: block !important; }
    .btn-check:checked + label { transform: scale(1.1); box-shadow: 0 0 10px rgba(0,0,0,0.2) !important; }
</style>
@endsection