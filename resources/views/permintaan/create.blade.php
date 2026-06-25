@extends('layouts.admin')

@section('title', 'Input Pengaduan')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-pencil-square"></i> Form Input Data</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('complaints.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tanggal Masuk</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Unit yg Mengeluh</label>
                    <select name="reporter_type" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Siapa --</option>
                        <option value="Pasien/Keluarga">Pasien / Keluarga</option>
                        <option value="Wartawan/Jurnalis">Wartawan / Media</option>
                        <option value="LSM">LSM</option>
                        <option value="Pemerhati Kesehatan">Pemerhati Kesehatan</option>
                        <option value="Masyarakat Umum">Masyarakat Umum</option>
                        <option value="Internal RS">Internal RS</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Nama Pelapor (Opsional)</label>
                    <input type="text" name="reporter_name" class="form-control" placeholder="Nama orangnya...">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Media Pengaduan</label>
                    <select name="source_id" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Media --</option>
                        @foreach($sources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25">

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Grade / Kegawatan</label>
                    <div class="d-flex flex-column gap-2">
                        <label class="btn btn-outline-danger btn-sm text-start">
                            <input type="radio" name="grade" value="Merah" required> 🔴 Merah (Gawat)
                        </label>
                        <label class="btn btn-outline-warning text-dark btn-sm text-start">
                            <input type="radio" name="grade" value="Kuning"> 🟡 Kuning (Sedang)
                        </label>
                        <label class="btn btn-outline-success btn-sm text-start">
                            <input type="radio" name="grade" value="Hijau"> 🟢 Hijau (Ringan)
                        </label>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Isi Keluhan</label>
                    <textarea name="description" class="form-control" rows="6" placeholder="Ketik keluhan lengkap disini..." required></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Unit Tujuan (Tertuduh)</label>
                    <select name="unit_destination" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Unit Tujuan --</option>
                        <option value="IGD">IGD</option>
                        <option value="Poli Anak">Poli Anak</option>
                        <option value="Poli Bedah">Poli Bedah</option>
                        <option value="Poli Penyakit Dalam">Poli Penyakit Dalam</option>
                        <option value="Poli Jantung">Poli Jantung</option>
                        <option value="Poli Mata">Poli Mata</option>
                        <option value="Poli THT">Poli THT</option>
                        <option value="Poli Saraf">Poli Saraf</option>
                        <option value="Poli Kulit & Kelamin">Poli Kulit & Kelamin</option>
                        <option value="Poli Gigi">Poli Gigi</option>
                        <option value="Poli Jiwa">Poli Jiwa</option>
                        <option value="Farmasi / Obat">Farmasi / Obat</option>
                        <option value="Kasir / Administrasi">Kasir / Administrasi</option>
                        <option value="Rawat Inap">Rawat Inap</option>
                        <option value="Security / Keamanan">Security / Keamanan</option>
                        <option value="Parkir">Parkir</option>
                        <option value="Fasilitas RS (WC/AC/Air)">Fasilitas RS (WC/AC/Air)</option>
                    </select>
                </div>
            </div>

            <div class="row bg-light p-3 rounded border mx-1 align-items-center">
                <div class="col-md-6 d-flex align-items-center gap-3">
                    <label class="form-label small fw-bold text-muted mb-0">Verifikasi Tanggal:</label>
                    <input type="date" class="form-control form-control-sm" style="width: 150px;" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6 text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <i class="bi bi-save"></i> SIMPAN DATA
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection