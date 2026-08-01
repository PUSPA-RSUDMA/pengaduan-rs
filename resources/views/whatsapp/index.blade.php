@extends('layouts.admin')
@section('title', 'Koneksi WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Kolom Uji Coba Kirim Pesan -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-send text-primary me-2"></i> Pengaturan & Uji Coba Pesan WhatsApp
                </div>
                <div class="card-body">
                    
                    {{-- Notifikasi --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle-fill me-1"></i> Sistem ini menggunakan API dari <strong>Fonnte.com</strong>. Pastikan perangkat Anda selalu terhubung (Connected) di dashboard website Fonnte agar pesan H-1 Logbook otomatis terkirim.
                    </div>

                    <form action="{{ route('whatsapp.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Tujuan WhatsApp</label>
                            <input type="text" name="nomor" class="form-control" value="085336102800" required>
                            <div class="form-text text-muted">Contoh: 085336102800 (Bisa menggunakan awalan 08 atau 62)</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Isi Pesan Uji Coba</label>
                            <textarea name="pesan" class="form-control" rows="5" required>Halo, ini adalah pesan uji coba dari sistem LaSehat. Jika pesan ini masuk, berarti sistem API Fonnte berjalan dengan baik.</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold w-100 py-2">
                            <i class="bi bi-cursor-fill me-2"></i> Kirim Pesan Tes Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection